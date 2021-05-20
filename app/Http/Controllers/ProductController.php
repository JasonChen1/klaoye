<?php

namespace App\Http\Controllers;

use App\Models\{Product,ProductImage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Product as ProductRequest;
// use Carbon\Carbon;
// use Excel;
use Cache;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $where = [];
        $orderField = 'id';
        $order = 'desc';
        $filter = (array)json_decode($request->filter);

        if(count($filter)>0){
            foreach ($filter as $key => $value) {
                if($key==='sortField'){
                    $orderField = $value;
                } else if($key==='sortOrder'){
                    $order = $value;
                } else{
                    array_push($where, [$key,'like','%'.$value.'%']);
                }
            }
            return Product::with(['details','images'])->orderBy($orderField,$order)->paginate(2);
        }

        return Cache::tags(['products'])->rememberForever('products.all.page_'.$request->page.'_admin', function (){
            return Product::with(['details','images'])->orderBy('id','desc')->paginate(2);
        });
    }

    public function setActive(Request $request){
        $product = Product::findOrFail($request->id);
        $product->update(['status'=>$request->status]);
        Cache::tags('products')->flush();
        return response()->json($product->status,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
 
        $prod = Product::create($request->all());

        if($request->images){
            foreach ($request->images as $image) {
                $name = $image->getClientOriginalName();
                $path = $image->storeAs('public/images',$name);
                $img = new ProductImage([
                    'image_url'=>$path
                ]);

                $prod->images()->save($img);
            }
        }
        
        return response()->json($prod->load(['details','images']),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
