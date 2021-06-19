<?php

namespace App\Http\Controllers;

use App\Models\{Testimonial,Product};
use Illuminate\Http\Request;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Testimonial as TestimonialRequest;
use Cache;

class TestimonialController extends Controller
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
            return Testimonial::with(['product','image'])->where($filter['query'])->orderBy($filter['orderField'],$filter['order'])->paginate(10);
        }

        return Cache::tags(['testimonials'])->rememberForever('testimonials.all.page_'.$request->page.'_admin', function (){
            return Testimonial::with(['product','image'])->orderBy('id','desc')->paginate(10);
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TestimonialRequest $request)
    {
        Product::findOrFail($request->product_id);
        $testi = Testimonial::create($request->all());
        Cache::tags('testimonials')->flush();
        return response()->json($testi->load(['product','image']),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function show(Testimonial $testimonial)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            "product_id"=>'required',
            "url" => 'required|string',
            "rating" => 'nullable|integer',
            "name" => 'required|string',
            "personal_des" => 'nullable|string',
            "description"=> 'nullable|string',
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(),422);
        }    

        Product::findOrFail($request->product_id);

        $testi = Testimonial::findOrFail($id);
        $testi->update($request->all());

        Cache::tags('testimonials')->flush();
        return response()->json($testi->load(['product','image']),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $testi = Testimonial::findOrFail($id);
        $testi->delete();
        Cache::tags('testimonials')->flush();
        return response()->json('done',204);
    }
}
