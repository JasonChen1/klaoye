<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\{Category,Product,Testimonial,Order,Enquiry};
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewOrder;
use App\Notifications\Enquiry as NotifucationEnquiry;
use App\Rules\StringSymbol;
use App\Traits\HelperTrait;
use Stripe\Stripe;
use Carbon\Carbon;
use App\Mail\Receipt;
use Mail;
use Cache;
use DB;

class ShopController extends Controller
{

	use HelperTrait;

	public function index(Request $request) {
		$ssr = $this->render($request->path());
		return view('shop',['ssr' => $ssr]);
	}

	private function render($path) {
		$renderer_source = File::get(base_path('node_modules/vue-server-renderer/basic.js'));
		$app_source = File::get(public_path('js/shop-server.js'));
		$v8 = new \V8Js();
		ob_start();

		$js = 
		<<<EOT
		var process = { env: { VUE_ENV: "server", NODE_ENV: "production" } }; 
		this.global = { process: process }; 
		var url = "$path";
		EOT;

		$v8->executeString($js);
		$v8->executeString($renderer_source);
		$v8->executeString($app_source);

		return ob_get_clean();
	}

	/*get all testimonials*/
	public function testimonials(){
		return Testimonial::with(['product','image'])->get();
	}

	/*get all categories*/
	public function categories(){
		return Category::with(['subcategories'])->get();
	}

	/*web header search base on product name*/
	public function search(Request $request){
		$v = Validator::make($request->all(), [
            "text"=>'alpha_num'
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(),422);
        } 

        $products = Product::with(['images'=>function($q){
        	$q->select('product_id','image_url');
        }])->where([['products.name','like','%'.$request->text.'%']])
        ->get(['products.id','products.name']);

        return response()->json($products,200);
	}

	/*get single product details*/
	public function details(Request $request){
		$prod = Product::findOrFail($request->id);

		return response()->json($prod->load(['details','images']),200);
	}

	/*get all products with category list*/
	public function products(Request $request){
		$filter = [
			'asc',
			'desc',
			'new',
			'old',
			'pasc',
			'pdesc',
		];

		$orderField='name';
		$order='asc';
		$where=[];

		if($request->category){
			array_push($where,['category_name',$request->category]);
		}

		$categories = Cache::tags(['categories'])->rememberForever('categories.all.page_front', function (){
			return Product::select(DB::raw('count(*) as count, category_name as name'))
			->whereNotNull('category_name')
			->groupBy('category_name')
			->get();
		});

		if($request->filter){
			if(in_array($request->filter, $filter)){
				switch ($request->filter) {
					case 'asc':
						$orderField = 'name';
						$order='asc';
						break;
					case 'desc':
						$orderField = 'name';
						$order='desc';
						break;
					case 'new':
						$orderField = 'id';
						$order='asc';
						break;
					case 'old':
						$orderField = 'id';
						$order='desc';
						break;
					case 'pasc':
						$orderField = 'price';
						$order='asc';
						break;
					case 'pdesc':
						$orderField = 'price';
						$order='desc';
						break;
				}
				$response['products'] = Product::with(['details','image'])->whereNotNull('category_id')->where($where)->orderBy($orderField,$order)->paginate(12);
			}
		}else{
			$response['products'] = Cache::tags(['products'])->rememberForever('products_front'.$request->page.'_category_'.$request->category, function () use($where){
				return Product::with(['details','image'])->whereNotNull('category_id')->where($where)->orderBy('id','desc')->paginate(12);
			});
		}

        $response['categories'] = $categories;

		return response()->json($response,200);
	}

	/*paypal checkout*/
	public function paypalCheckout(Request $request){
		$data = $this->createNewOrder($request);
		if(gettype($data)!=='array'){
			return $data;
		}

		//'0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款'
		$data['order']['status'] = $request->pStatus==='COMPLETED'?1:0;
		$data['order']['paid'] = $request->pStatus==='COMPLETED'?true:false;
		$data['order']['payment_type'] = 'Paypal';

		DB::transaction(function() use ($data){
			$order = Order::create($data['order']);
			$order->items()->saveMany($data['orderItems']);
			$order->shipping()->save($data['shipping']);
		},3);

		$shippingAddress = $request->address.' '.$request->city.' '.$request->state.' '.$request->country.' '.$request->postal_code;

		// reduce occupied
		foreach ($data['prodCallback'] as $prod) {
			$p = Product::findOrFail($prod['id']);
			// guest checkout will have to add to occupied stocks
			$p->update(['occupied' => $p->occupied + $prod['num']]);
		}

		// send receipt
		Mail::to($request->email)
		->queue((new Receipt($data['prodCallback'],$data['totals'],$data['order']['no'],$shippingAddress))
			->onQueue('mail-queue'));

		$orderEmail = env('ORDER_EMAIL');
        // send new order
		Notification::route('mail', $orderEmail)->notify((new NewOrder($data['order']['no']))->onQueue('mail-queue'));

		return response()->json('success',200);
	}

	/* Stripe checkout */
	public function checkout(Request $request){
		$data = $this->createNewOrder($request);
		if(gettype($data)!=='array'){
			return $data;
		}

		DB::transaction(function() use ($data){
			$order = Order::create($data['order']);
			$order->items()->saveMany($data['orderItems']);
			$order->shipping()->save($data['shipping']);
		},3);

		$shippingAddress = $request->address.' '.$request->city.' '.$request->state.' '.$request->country.' '.$request->postal_code;
		// pay with stripe
		$ss = env('STRIPE_SECRET');
		Stripe::setApiKey($ss);
		try {
			$intent = \Stripe\PaymentIntent::create([
				'amount' => $data['totals']['total']*100,
				'currency' => 'USD',
				'description' => 'New Order - '.$data['order']['no'],
				'metadata' => [
					'integration_check' => 'accept_a_payment'
				],
			]);

			$response =[
				'client_secret' => $intent->client_secret,
				'order_no'=>$data['order']['no'],
				'email'=>$request->email,
				'totals'=>$data['totals'],
				'address'=>$shippingAddress,
				'products'=>$data['prodCallback']
			];

			return response()->json($response,200);
		} catch (\Exception $e) {
			return response()->json($e->getMessage(),402);
		}
	}

	/*stripe callback - update product status (occupied,cart)*/
	public function stripeCallback(Request $request){
		$email = $request->email;
		$order = Order::where('no',$request->order_no)->first();

		// reduce occupied
		foreach ($request->products as $prod) {
			$p = Product::findOrFail($prod['id']);
			// guest checkout will have to add to occupied stocks
			$p->update(['occupied' => $p->occupied + $prod['num']]);
		}

		//'0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款'
		$order->update([
			'status'=>1,
			'paid'=>true,
			'payment_type'=>'Stripe'
		]);
	
		// send receipt
		Mail::to($email)
            ->queue((new Receipt($request['products'],$request['totals'],$request->order_no,$request->address))
            ->onQueue('mail-queue'));

        $orderEmail = env('ORDER_EMAIL');
		
        // send new order
		Notification::route('mail', $orderEmail)->notify((new NewOrder($request->order_no))->onQueue('mail-queue'));

        return response()->json('success',200);
	}

	/*send customer enquiries*/
	public function enquiry(Request $request){

        $v = Validator::make($request->all(), [
            "email" => "required|email",
            "phone" => "nullable|string",
            "name" => ['required','string'],
            "product" => ['required','string'],
            "message" => ['required','string'],
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(),422);
        } 

        $enquiry = Enquiry::create($request->all());
        $orderEmail = env('ORDER_EMAIL');
        Notification::route('mail', $orderEmail)->notify((new NotifucationEnquiry($enquiry))->onQueue('mail-queue'));

        return response()->json('sent',204);
	}

	public function rename(Request $request){
		$path = 'C:/Users/Jason/Desktop/Products/冠通/置物架深棕色/Images';
		if ($handle = opendir($path)) {
			$count = 1;
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$newName = "cabinet brown2".$count;//改名字
					rename($path.'/'.$entry, $path.'/'.$newName.'.jpg');
					$count++;
				}
			}
			closedir($handle);
			return 'done';
		}
	}
}

