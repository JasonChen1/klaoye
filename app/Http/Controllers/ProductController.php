<?php

namespace App\Http\Controllers;

use App\Models\{Product,ProductImage,ProductDetail};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Product as ProductRequest;
use App\Traits\HelperTrait;
use App\Imports\ProductImport;
use Excel;
use Cache;
use Image;

class ProductController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {       
        $filter = $this->filter($request);
       
        if($filter['filter']){
            return Product::with(['details','images'])->where($filter['query'])->orderBy($filter['orderField'],$filter['order'])->paginate(12);
        }

        return Cache::tags(['products'])->rememberForever('products.all.page_'.$request->page.'_admin', function (){
            return Product::with(['details','images'])->orderBy('id','desc')->paginate(12);
        });
    }

    /*set product status 0:inactive 1:active*/
    public function setActive(Request $request){
        $product = Product::findOrFail($request->id);
        $product->update(['status'=>$request->status]);
        Cache::tags('products')->flush();
        Cache::tags('categories')->flush();
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
        if(!$request->status){
            $request['status'] = 1;
        }

        $prod = Product::create($request->except(['sub_category_id','sub_category_name']));

        if($request->images){
            foreach ($request->images as $image) {
                $name = $image->getClientOriginalName();
                $path = $image->storeAs('images',$name,'public');
                
                $image->storeAs('thumbnail/images', $name,'public');
                $thumbnail = public_path('storage/thumbnail/images/'.$name);
                $this->createThumbnail($thumbnail, 90, 90);

                $img = new ProductImage([
                    'image_url'=>$path
                ]);

                $prod->images()->save($img);
            }
        }
        Cache::tags('products')->flush();
        Cache::tags('categories')->flush();
        return response()->json($prod->load(['details','images']),200);
    }

    /*upload products*/
    public function upload(Request $request){
        $v = Validator::make([
            'file'  => $request->file,
            'extension' => strtolower($request->file->getClientOriginalExtension()),
        ],
        [
            'file'  => 'required',
            'extension' => 'required|in:csv,xlsx,xls,odt,ods,odp',
        ]);
        if ($v->fails()) {
            abort($v->errors(),400);
        }

        $rows = Excel::toArray(new ProductImport, $request->file('file')); 
        array_shift($rows[0]);
        $fileData = $rows[0];

        $insertData = $this->formatInsert($fileData);
        Product::insert($insertData);
        Cache::tags('products')->flush();
        return response()->json('success',204);
    }

    /**
     * Create a thumbnail of specified size
     *
     * @param string $path path of thumbnail
     * @param int $width
     * @param int $height
     */
    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }

    /*remove product image*/
    public function deleteImage(Request $request,$id){
        $image = ProductImage::findOrFail($id);
        $image->delete();
        Cache::tags('products')->flush();
        return response()->json('done',204);
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
    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            "name"=>'string|required',
            "code" => 'required|string',//|exists:products,code',
            "price" => 'required|numeric',
            "status" => 'nullable|string',
            "stock" => 'required|integer',
            "size" => 'nullable|string',
            "description"=> 'nullable|string',
            'images' => 'array|nullable',
            'images.*'=>'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(),422);
        }    

        $prod = Product::findOrFail($id);
        if($request->discount=='null'){
            $request['discount']=0.00;
        }

        if($request->status=='null'){
            $request['status'] = 1;
        }
        if($request->delivery=='null'){
            $request['delivery']=0.00;
        }

        $prod->update($request->except(['sub_category_id','sub_category_name']));

        if($request->images){
            foreach ($request->images as $image) {
                $name = $image->getClientOriginalName();
                $path = $image->storeAs('images', $name, 'public');
                $img = new ProductImage([
                    'image_url'=>$path
                ]);

                $image->storeAs('thumbnail/images', $name, 'public');
                $thumbnail = public_path('storage/thumbnail/images/'.$name);
                $this->createThumbnail($thumbnail, 90, 90);

                $prod->images()->save($img);
            }
        }

        Cache::tags('products')->flush();
        Cache::tags('categories')->flush();

        return response()->json($prod->load(['details','images']),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prod = Product::findOrFail($id);
        $prod->delete();
        Cache::tags('products')->flush();
        Cache::tags('categories')->flush();
        return response()->json('done',204);
    }

    /*add a colour to product*/
    public function addColour(Request $requset){

        $prod = Product::findOrFail($requset->id);
        $color = ProductDetail::create([
            'product_id'=>$requset->id,
            'color_code'=>$requset->color_code
        ]);
        Cache::tags('products')->flush();
        return response()->json($color,200);
    }

    /*delete colour from product*/
    public function deleteColour($id){
        $color = ProductDetail::findOrFail($id);
        $color->delete();
        Cache::tags('products')->flush();
        return response()->json('done',200);
    }
}
