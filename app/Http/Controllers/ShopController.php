<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\{User,Category,SubCategory,Product,ProductImage,ProductDetail,Testimonial,Order,OrderDetail,OrderShippingAddress};
use Illuminate\Support\Facades\Validator;
use App\Rules\StringSymbol;
use Stripe\Stripe;
use Carbon\Carbon;
use App\Mail\Receipt;
use Mail;
use Cache;
use DB;

class ShopController extends Controller
{
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
				$response['products'] = Product::with(['details','image'])->whereNotNull('category_id')->where($where)->orderBy($orderField,$order)->paginate(10);
			}
		}else{
			$response['products'] = Cache::tags(['products'])->rememberForever('products_front'.$request->page.'_category_'.$request->category, function () use($where){
				return Product::with(['details','image'])->whereNotNull('category_id')->where($where)->orderBy('id','desc')->paginate(10);
			});
		}

        $response['categories'] = $categories;

		return response()->json($response,200);
	}

	/* Stripe checkout */
	public function checkout(Request $request){
		// direct checkout
		if($request->prod){
			//todo
		}

		// pay existing order
		if($request->order_no){
			return $this->orderCheckout($request);
		}

		// shipping address must be exists
		$v = Validator::make($request->all(), [
			"email" => "required|email",
			"phone" => "nullable|integer",
			"name" => ['required',new StringSymbol()],
			"address" => ['required',new StringSymbol()],
			"city" => ['required',new StringSymbol()],
			"state" => ['nullable',new StringSymbol()],
			"country" => ['required',new StringSymbol()],
			"postal_code" => "required|integer"
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(),422);
        } 

		if(empty($request->cartData)){
			return response()->json('No product avaiable for checkout',400);
		}

		// create order
		$orderNo = Carbon::now()->timestamp.'G';//G- guest checkout U-user checkout

		// shipping address for order
		$shipping = new OrderShippingAddress([
			'email'=>$request->email,
			'phone'=>$request->phone,
			'name'=>$request->name,
			'address'=>$request->address,
			'city'=>$request->city,
			'state'=>$request->state,
			'country'=>$request->country,
			'postal_code'=>$request->postal_code,
		]);

		// calculate order item totals
		$orderTotal = $orderSubtotal = $orderDiscountTotal = $orderDeliveryTotal = 0;
		$orderItems = [];
		$prodCallback=[];
		foreach($request->cartData as $prod){
			$item = Product::findOrFail($prod['id']);
			$discount_total = $item->discount * $prod['num'];
			$delivery_total = $item->delivery * $prod['num'];
			$subtotal =  $item->price * $prod['num'];
			$discounted = $subtotal-$discount_total;
			$total = $subtotal - $discount_total + $delivery_total;
			$color_id = array_key_exists('color_id', $prod) ? $prod['color_id']:'';
			$color_code = array_key_exists('color_code', $prod) ? $prod['color_code']:'';

			array_push($prodCallback,[
				'id'=>$prod['id'],
				'code'=>$item->code,
				'num'=>$prod['num'],
				'name'=>$item->name,
				'color_code'=>$color_code,
				'price'=>$item->price,
				'subtotal'=>$subtotal,
				'total'=>$total,
				'discount'=>$item->discount,
				'delivery'=>$delivery_total,
				'discounted'=>$discounted,
				'discount_total'=>$discount_total,
			]);

			$item = new OrderDetail([
				'num'=>$prod['num'],
				'price'=>$item->price,
				'subtotal'=>$subtotal,
				'total'=>$total,
				'discount'=>$item->discount,
				'delivery'=>$delivery_total,
				'discounted'=>$discounted,
				'discount_total'=>$discount_total,
				'detail_id'=>$color_id,
				'color_code'=>$color_code,
				'product_id'=>$prod['id'],
			]);

			array_push($orderItems, $item);
			$orderTotal+=$total;
			$orderSubtotal+=$subtotal;
			$orderDiscountTotal+=$discount_total;
			$orderDeliveryTotal+=$delivery_total;
		}
		//'0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款'
		$order = [
			'no'=>$orderNo,
			'paid'=>false,
			'status'=>0,
			'subtotal'=>$orderSubtotal,
			'discount_total'=>$orderDiscountTotal,
			'delivery_total'=>$orderDeliveryTotal,
			'total'=>$orderTotal,
		];

		DB::transaction(function() use ($order,$shipping,$orderItems){
			$order = Order::create($order);
			$order->items()->saveMany($orderItems);
			$order->shipping()->save($shipping);
		},3);

		$shippingAddress = $request->address.' '.$request->city.' '.$request->state.' '.$request->country.' '.$request->postal_code;
		// pay with stripe
		$ss = env('STRIPE_TEST_SECRET');
		Stripe::setApiKey($ss);
		try {
			$intent = \Stripe\PaymentIntent::create([
				'amount' => $orderTotal*100,
				'currency' => 'USD',
				'description' => 'New Order - '.$orderNo,
				'metadata' => [
					'integration_check' => 'accept_a_payment'
				],
			]);

			$response =[
				'client_secret' => $intent->client_secret,
				'order_no'=>$orderNo,
				'email'=>$request->email,
				'totals'=>[
					'total'=>$orderTotal,
					'subtotal'=>$orderSubtotal,
					'discount_total'=>$orderDiscountTotal,
					'delivery_total'=>$orderDeliveryTotal,
				],
				'address'=>$shippingAddress,
				'products'=>$prodCallback
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
			'paid'=>true
		]);

		// send receipt
		Mail::to($email)
            ->queue((new Receipt($request['products'],$request['totals'],$request->order_no,$request->address))
            ->onQueue('mail-queue'));

        return response()->json('success',200);
	}

	/*check out order that already exist but have not being paid*/
	public function orderCheckout(Request $request){

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

