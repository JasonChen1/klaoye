<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product};
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

	public function cart(Request $request){
		$user = $request->user();
		if($user){
			return response()->json($user->cart()->count(),200);
		}
		return response()->json('user not found',404);
	}

	/*add to user cart*/
	public function addtoCart(Request $request){
		$user = $request->user();

		// check if stock avaliable
		$prod = Product::findOrFail($request->id);
		if($prod->stock >= ($prod->occupied+1)){
			
			$exists = $user->cart()->where([['product_id',$prod->id],['detail_id',$request->detail_id]])->first();
			
			$num = 1;
			$total = $discount_total = $subtotal =$discount= 0;
			
			// if different color save as new record
			if(!$exists){
				$total = $prod->price * $num;
				$discount_total = $prod->discount * $num;
			}else{
				$num = $exists->num+1;
				$total = $prod->price * $num;
				$discount_total = $prod->discount * $num;	
			}

			$subtotal = $total - $discount_total;

			return DB::transaction(function () use($user,$prod,$num,$total,$subtotal,$discount_total,$request) {

				$user->cart()->updateOrCreate(
					[
						'product_id'=>$prod->id,
						'detail_id'=>$request->detail_id
					],
					[
						'num'=>$num,
						'price'=>$prod->price,
						'discount'=>$prod->discount,
						'color_code'=>$request->color_code,
						'subtotal'=>$subtotal,
						'total'=>$total,
						'discount_total'=>$discount_total,
					]);

				$prod->update(['occupied'=>$prod->occupied+1]);
			}, 3);
		}else{
			return response()->json(1,400);
		}
	}

}
