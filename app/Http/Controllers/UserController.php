<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product,Cart,ProductDetail,ProductImage};
use DB;

class UserController extends Controller
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

	/*get number of item exists in cart*/
	public function cartCount(Request $request){	
		$user = $request->user();
		return response()->json($user->cart->items()->count(),200);
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

	/*get all item from cart*/
	public function cart(Request $request){
		$user = $request->user();
		$cart = $user->cart;

        $items = $cart->items()->with(['product'=>function($q){
        	$q->with('images');
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

        $response = [
        	'total'=>$cart,
        	'items'=>$itemsArray
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

}
