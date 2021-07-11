<?php

namespace App\Http\Controllers;

use App\Models\{Order,OrderDetail,OrderShippingAddress};
use Illuminate\Http\Request;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Testimonial as TestimonialRequest;
use Cache;

class OrderDetailController extends Controller
{

    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $items = OrderDetail::where('order_id',$id)->with(['product'=>function($q){
            $q->with('image');
        }])->get();
        
        $shipping = OrderShippingAddress::where('order_id',$id)->first();
        
        $response = [
            'items'=>$items,
            'shipping'=>$shipping
        ];

        return response()->json($response,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderDetail $orderDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderDetail $orderDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderDetail $orderDetail)
    {
        //
    }
}
