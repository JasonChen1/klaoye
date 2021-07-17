<?php

namespace App\Http\Controllers;

use App\Models\{Category,SubCategory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Category as CategoryRequest;
use App\Traits\HelperTrait;
use Cache;

class CategoryController extends Controller
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
            return Category::with(['subcategories'])->where($filter['query'])->orderBy($filter['orderField'],$filter['order'])->paginate(10);
        }
 
        return Cache::tags(['categories'])->rememberForever('categories.all.page_'.$request->page.'_admin', function (){
            return Category::with(['subcategories'])->orderBy('id','desc')->paginate(10);
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::firstOrCreate([
            'name'=>$request->category
        ]);

        $subcategory = SubCategory::firstOrCreate(
            ['name'=>$request->subcategory],
            ['category_id'=>$category->id]
        );

        Cache::tags('categories')->flush();

        return response()->json($category->load(['subcategories']),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($id==='all'){
            return Cache::tags(['categories'])->rememberForever('categories.all', function (){
                return Category::with(['subcategories'])->orderBy('id','desc')->get();
            });
        }

        return response()->json('todo',200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $nameCheck = Category::where('name',$request->name)->first();
        //if other categories has the same name do not update
        if($nameCheck->id !==$category->id){
            return response()->json('category exists',400);
        }
        $category->update([
            'name'=>$request->name
        ]);

        foreach ($request->subcategories as $sub) {
            $sc = SubCategory::findOrFail($sub['id']);
            $sc->update([
                'name'=>$sub['name']
            ]);
        }

        Cache::tags('categories')->flush();
        return response()->json('done',200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        Cache::tags('categories')->flush();

        return response()->json('done',204);
    }

    public function destroySubCategory($id){
        $category = SubCategory::findOrFail($id);
        $category->delete();
        Cache::tags('categories')->flush();

        return response()->json('done',204);
    }
}
