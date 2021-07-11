<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product,User,Order};
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewOrder;
use App\Rules\StringSymbol;
use App\Traits\HelperTrait;
use App\Mail\Receipt;
use Stripe\Stripe;
use Mail;
use DB;

class UserController extends Controller
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

	/*get number of item exists in cart*/
	public function cartCount(Request $request){	
		$user = $request->user();
		$count = $user->cart ? $user->cart->items()->count() : 0;
		return response()->json($count,200);
	}

	/*empty shopping cart*/
	public function emptyCart(Request $request){
		$user = $request->user();
		$items = $user->cart->items;
		
		return DB::transaction(function () use($user,$items) {
			foreach ($items as $item) {
				$prod = $item->product;
				$prod->update(['occupied'=>$prod->occupied-$item->num]);
			}
			$user->cart->items()->delete();
			$user->cart()->update([
				'total'=>0,
				'subtotal'=>0,
				'delivery_total'=>0,
				'discount_total'=>0
			]);
		}, 3);

		return response()->json('done',204);
	}

	/*return user's details and address */
	public function details(Request $request){
		$user = $request->user();
		// add order here 
		return response()->json($user->load(['address','orders']),200);
	}

	/*update or add user address*/
	public function updateAddress(Request $request){
		$v = Validator::make($request->all(), [
            "phone" => "nullable|integer",
            "address" => ['required',new StringSymbol()],
            "city" => ['required',new StringSymbol()],
            "state" => ['nullable',new StringSymbol()],
            "country" => ['required',new StringSymbol()],
            "postal_code" => "required|integer"
        ]);

		if ($v->fails()) {
			return response()->json($v->errors(),422);
		}

		$user = $request->user();

		$request['name'] = $user->name;
		$request['email'] = $user->email;
	
		$user->address()->updateOrCreate(
			['user_id'=>$user->id],
			$request->all(),
		);

		return response()->json($user->address);
	}

	/*update user emial & name*/
	public function updateDetails(Request $request){
		$v = Validator::make($request->all(), [
			"name" => ['required',new StringSymbol()],
			'email' => ['required', 'string', 'email', 'max:255'],
		]);

		if ($v->fails()) {
			return response()->json($v->errors(),422);
		}

		$user = $request->user();
		// if user has different email but exists in DB, dont do anything
		$checkUser = User::where('email',$request->email)->first();
		if($user->email!=$checkUser->email && $checkUser){
			return response()->json('user exists',401);
		}

		$user->update($request->only(['name','email']));

		return response()->json($user,200);
	}

	/*get all item from cart*/
	public function cart(Request $request){
		$user = $request->user();
		$cart = $user->cart;

		$items = $cart->items()->with(['product'=>function($q){
			$q->with('image');
		}])->get()->toArray();

		$itemsArray = [];
		foreach($items as $key=>$item){
			foreach ($item as $pk => $prod) {
				if($pk!='product'){
					$itemsArray[$key][$pk] = $prod;
				}else{
					foreach ($prod as $p => $pv) {
						if($p!=='id'){
							$itemsArray[$key][$p] = $pv;
						}
					}
				}
			}
		}

		$address = $user->address;
		$address['name'] = $user->name;
		$address['email'] = $user->email;

		$response = [
			'total'=>$cart,
			'items'=>$itemsArray,
			'address'=>$address,
		];
		
		return response()->json($response,200);
	}

	/*delete item from cart*/
	public function deleteFromCart(Request $request, $id){
		$user = $request->user();
		$item = $user->cart->items()->findOrFail($id);
		$prod = $item->product;

		$cart = [
			'total'=>$user->cart->total-$item->total,
			'subtotal'=>$user->cart->subtotal-$item->subtotal,
			'discount_total'=>$user->cart->discount_total-$item->discount_total,
			'delivery_total'=>$user->cart->delivery_total-$item->delivery_total,
		];
		DB::transaction(function () use($user,$item,$cart,$prod) {
			$user->cart()->update($cart);
			$prod->update(['occupied'=>$prod->occupied-$item->num]);
			$item->delete();
		}, 3);

		return response()->json($cart,200);
	}

	/*add to user cart*/
	public function addtoCart(Request $request){
		$user = $request->user();

		// check if stock avaliable
		$prod = Product::findOrFail($request->id);
		if($prod->stock >= ($prod->occupied+1)){
			$exists = false;
			$cart = $user->cart;
			if($cart){
				$exists = $user->cart->items()->where([['product_id',$prod->id],['detail_id',$request->detail_id]])->first();
			}else{
				$cart = $user->cart()->create();
			}
			
			$num = 1;
			$total = $discount_total = $subtotal =$discount=$delivery_total= 0;
			
			// if different color save as new record
			if($exists){
				$num = $exists->num+1;
			}
			$subtotal = $prod->price * $num;
			$discount_total = $prod->discount * $num;	
			$delivery_total = $prod->delivery * $num;

			$total = $subtotal - $discount_total + $delivery_total;

			DB::transaction(function () use($prod,$num,$total,$subtotal,$discount_total,$request,$cart,$delivery_total) {
				$cart->items()->updateOrCreate(
					[
						'product_id'=>$prod->id,
						'detail_id'=>$request->detail_id
					],
					[
						'num'=>$num,
						'price'=>$prod->price,
						'discount'=>$prod->discount,
						'delivery'=>$prod->delivery,
						'color_code'=>$request->color_code,
						'subtotal'=>$subtotal,
						'total'=>$total,
						'discount_total'=>$discount_total,
						'discounted'=>$prod->price-$prod->discount,
					]);

				$cart->update([
					'delivery_total'=>$cart->delivery_total+$delivery_total,
					'discount_total'=>$cart->discount_total+$discount_total,
					'subtotal'=>$cart->subtotal+$subtotal,
					'total'=>$cart->total+$total
				]);
				$prod->update(['occupied'=>$prod->occupied+1]);
			}, 3);
			return response()->json('done',200);
		}else{
			return response()->json(1,400);
		}
	}

	/*update cart item quantity*/
	public function updateQuantity(Request $request,$id){
		$user = $request->user();

		if($request->num<1){
			return response()->json('Quantity must be greater than 0',400);
		}

		$item = $user->cart->items()->findOrFail($id); 
		$prod = $item->product;
		$currentOccupied = $prod->occupied-$item->num;

		if($prod->stock < ($request->num+$currentOccupied)){
			return response()->json('Stock not enough',400);
		}

		// calculate total
		$numDiff = $request->num - $item->num;
		$subtotal = $prod->price*$numDiff;
		$discount_total = $prod->discount*$numDiff;
		$delivery_total = $prod->delivery*$numDiff;
		$total = $subtotal - $discount_total + $delivery_total;

		// cart total
		$cart = [
			'total'=>$user->cart->total+$total,
			'subtotal'=>$user->cart->subtotal+$subtotal,
			'discount_total'=>$user->cart->discount_total+$discount_total,
			'delivery_total'=>$user->cart->delivery_total+$delivery_total,
		];

		// product total
		$pSubtotal = $prod->price * $request->num;
		$pDiscount_total = $prod->discount * $request->num;
		$pDelivery_total = $prod->delivery * $request->num;

		$newItem = [
			'num'=>$request->num,
			'subtotal'=>$pSubtotal,
			'discount_total'=>$pDiscount_total,
			'delivery_total'=>$pDelivery_total,
			'total'=>$pSubtotal-$pDiscount_total,
			'occupied'=>$prod->occupied+$request->num,
		];

		DB::transaction(function () use($user,$item,$cart,$request,$prod,$newItem,$currentOccupied) {
			$user->cart()->update($cart);
			$item->update($newItem);
			$prod->update(['occupied'=>$currentOccupied+$request->num]);
		}, 3);

		$res = [
			'cart'=>$cart,
			'item'=>$newItem
		];

		return response()->json($res,200);
	}

	/*get user address*/
	public function address(Request $request){
		$user = $request->user();
		return response()->json($user->address,200);
	}

	/*paypal checkout*/
	public function paypalCheckout(Request $request){
		$user = $request->user();
		if(!$request->prod){
			$cart = $user->cart->items->toArray();
			$request['cartData'] = $cart;
		}

		$data = $this->createNewOrder($request,true);
		if(gettype($data)!=='array'){
			return $data;
		}

		//'0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款'
		$data['order']['status'] = $request->pStatus==='COMPLETED'?1:0;
		$data['order']['paid'] = $request->pStatus==='COMPLETED'?true:false;
		$data['order']['payment_type'] = 'Paypal';
		$data['order']['user_id'] = $user->id;

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

		// clear cart
		if(!$request->prod){
			$user->cart->delete();
		}

		// send receipt
		Mail::to($user->email)
		->queue((new Receipt($data['prodCallback'],$data['totals'],$data['order']['no'],$shippingAddress))
			->onQueue('mail-queue'));

		$orderEmailStr = env('ORDER_EMAIL');
		$orderEmail = json_decode($orderEmailStr);

        // send new order
		Notification::route('mail', $orderEmail)->notify((new NewOrder($data['order']['no']))->onQueue('mail-queue'));

		return response()->json('success',200);
	}

	/*stripe checkout*/
	public function checkout(Request $request){
		$user = $request->user();
		if(!$request->prod){
			$cart = $user->cart->items->toArray();
			$request['cartData'] = $cart;
		}

		$data = $this->createNewOrder($request,true);
		if(gettype($data)!=='array'){
			return $data;
		}

		$data['order']['user_id'] = $user->id;
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
				'products'=>$data['prodCallback'],
				'prod'=>$request->prod
			];

			return response()->json($response,200);
		} catch (\Exception $e) {
			return response()->json($e->getMessage(),402);
		}
	}

	/*stripe callback*/
	public function stripeCallback(Request $request){
		$user = $request->user();
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
		

		// clear cart
		if(!$request->prod){
			$user->cart->delete();
		}

		// send receipt
		Mail::to($email)
            ->queue((new Receipt($request['products'],$request['totals'],$request->order_no,$request->address))
            ->onQueue('mail-queue'));

        $orderEmailStr = env('ORDER_EMAIL');
		$orderEmail = json_decode($orderEmailStr);

        // send new order
		Notification::route('mail', $orderEmail)->notify((new NewOrder($request->order_no))->onQueue('mail-queue'));

        return response()->json('success',200);
	}

	/*get all orders from current user*/
	public function getOrders(Request $request){
		$user = $request->user();

		$filter = $this->filter($request);

        if($filter['filter']){
            return Order::where($filter['query'])->where('user_id',$user->id)->orderBy($filter['orderField'],$filter['order'])->paginate(10);
        }
               
        return Order::where('user_id',$user->id)->orderBy('id','desc')->paginate(10);
	}
}
