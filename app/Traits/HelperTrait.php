<?php

namespace App\Traits;
use Illuminate\Http\Request;

trait HelperTrait
{
	public function filter(Request $request){
		$response =[];
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
        }
        $response=[
        	'filter'=>count($filter)>0?true:false,
        	'query'=>$where,
        	'orderField'=>$orderField,
        	'order'=>$order
        ];

        return $response;
	}
}