<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\{User,Category,SubCategory,Product,ProductImage,ProductDetail};
use Illuminate\Support\Facades\Validator;
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
				$response['products'] = Product::with(['details','images'])->whereNotNull('category_id')->where($where)->orderBy($orderField,$order)->paginate(10);
			}
		}else{
			$response['products'] = Cache::tags(['products'])->rememberForever('products_front'.$request->page.'_category_'.$request->category, function () use($where){
				return Product::with(['details','images'])->whereNotNull('category_id')->where($where)->orderBy('id','desc')->paginate(10);
			});
		}

        $response['categories'] = $categories;

		return response()->json($response,200);
	}
	
}
