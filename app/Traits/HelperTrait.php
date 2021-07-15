<?php

namespace App\Traits;
use Illuminate\Http\Request;
use App\Models\{Product,OrderShippingAddress,OrderDetail,Category};
use Illuminate\Support\Facades\Validator;
use App\Rules\StringSymbol;
use Carbon\Carbon;

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

    // 格式化批量更新
    public function bulkUpdate($rows,$table,$fields){
        $query = 'UPDATE '.$table.' SET ';
        foreach ($fields as $field) {
            $cases[$field] = ' CASE ';
        }
        $now = Carbon::now()->toDateTimeString();
        $codes = [];
        foreach ($rows as $val) {
            $code = $val['code'];
            array_push($codes, $code);
            foreach ($cases as $key => $case) {
                if($key=='updated_at'){
                    $cases[$key] .= ' WHEN ( `code` = '.$val['code'].' )  THEN "'.$now.'" ';
                }else {
                    $cases[$key] .= ' WHEN ( `code` = '.$val['code'].' )  THEN "'.$val[$key].'"';
                }
            }
        }

        foreach ($cases as $key => $case) {
            $cases[$key] .= ' END';
        }

        foreach ($cases as $key => $value) {
            $query .= '`'.$key.'` = '.$value.',';
        }

        $codes = implode(',', $codes);
        $query = substr($query,0,-1).' `code` IN ('.$codes.')';
        return $query;
    }

    // 清除所有符号、/、<>、html字符
    private function formatValue($val){
        $val = trim($val);
        $val = stripslashes($val);
        $val = htmlspecialchars($val);
        $val = strip_tags($val);
        return $val;
    }

     // 格式化导入保存的数据
    public function formatInsert($rows){
        $now = Carbon::now()->toDateTimeString();
        $response = [];

        for ($i=0; $i < count($rows); $i++) {
            if($cid = Category::where('name',$rows[$i][2])->first())
            $insertD = [
                'code'=>$rows[$i][0],
                'name'=>$rows[$i][1],
                'category_id'=>$cid->id,
                'category_name'=>$rows[$i][2],
                'base_price'=>$rows[$i][3],
                'price'=>$rows[$i][4],
                'size'=>$rows[$i][5].'x'.$rows[$i][6].'x'.$rows[$i][7],
                'stock'=>$rows[$i][8],
                'description'=>$rows[$i][9],
                'created_at'=>$now,
                'updated_at'=>$now
            ];

            array_push($response, $insertD);
            
        }
        return $response;
    }

    /*creating new order with order details and shipping address*/
    public function createNewOrder(Request $request,$type=false){
        // pay existing order
        if($request->order_no){
            return $this->orderCheckout($request);
        }

        // shipping address must be exists
        $v = Validator::make($request->all(), [
            "email" => "required|email",
            "phone" => "nullable|integer",
            "name" => ['required','string'],
            "address" => ['required','string'],
            "city" => ['required','string'],
            "state" => ['nullable','string'],
            "country" => ['required','string'],
            "postal_code" => "required|integer"
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(),422);
        } 

        if(empty($request->cartData)){
            return response()->json('No product avaiable for checkout',400);
        }

        $checkoutType = $type ? 'U' : 'G';
        // create order
        $orderNo = Carbon::now()->timestamp.$checkoutType;//G- guest checkout U-user checkout

        // shipping address for order
        $shipping = new OrderShippingAddress([
            'email'=>$request->email,
            'phone'=>$request->phone,
            'name'=>$request->name,
            'address'=>$request->address,
            'city'=>$request->city,
            'state'=>$request->state,
            'country'=>$request->country,
            'postal_code'=>$request->postal_code,
        ]);

        // calculate order item totals
        $orderTotal = $orderSubtotal = $orderDiscountTotal = $orderDeliveryTotal = 0;
        $orderItems = [];
        $prodCallback=[];
        foreach($request->cartData as $prod){
            $id = $type ? $prod['product_id'] : $prod['id'];
            $item = Product::findOrFail($id);
            $discount_total = $item->discount * $prod['num'];
            $delivery_total = $item->delivery * $prod['num'];
            $subtotal =  $item->price * $prod['num'];
            $discounted = $subtotal-$discount_total;
            $total = $subtotal - $discount_total + $delivery_total;
            $color_id = array_key_exists('color_id', $prod) ? $prod['color_id']:'';
            $color_code = array_key_exists('color_code', $prod) ? $prod['color_code']:'';

            array_push($prodCallback,[
                'id'=>$id,
                'code'=>$item->code,
                'num'=>$prod['num'],
                'name'=>$item->name,
                'color_code'=>$color_code,
                'price'=>$item->price,
                'subtotal'=>$subtotal,
                'total'=>$total,
                'discount'=>$item->discount,
                'delivery'=>$delivery_total,
                'discounted'=>$discounted,
                'discount_total'=>$discount_total,
            ]);

            $item = new OrderDetail([
                'num'=>$prod['num'],
                'price'=>$item->price,
                'subtotal'=>$subtotal,
                'total'=>$total,
                'discount'=>$item->discount,
                'delivery'=>$delivery_total,
                'discounted'=>$discounted,
                'discount_total'=>$discount_total,
                'detail_id'=>$color_id,
                'color_code'=>$color_code,
                'product_id'=>$id,
            ]);

            array_push($orderItems, $item);
            $orderTotal+=$total;
            $orderSubtotal+=$subtotal;
            $orderDiscountTotal+=$discount_total;
            $orderDeliveryTotal+=$delivery_total;
        }
        //'0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款'
        $order = [
            'no'=>$orderNo,
            'paid'=>false,
            'status'=>0,
            'subtotal'=>$orderSubtotal,
            'discount_total'=>$orderDiscountTotal,
            'delivery_total'=>$orderDeliveryTotal,
            'total'=>$orderTotal,
        ];

        $response = [
            'order'=>$order,
            'shipping'=>$shipping,
            'prodCallback'=>$prodCallback,
            'orderItems'=>$orderItems,
            'totals'=>[
                'total'=>$orderTotal,
                'subtotal'=>$orderSubtotal,
                'discount_total'=>$orderDiscountTotal,
                'delivery_total'=>$orderDeliveryTotal,
            ]
        ];

        return $response;
    }

    /*check out order that already exist but have not being paid*/
    public function orderCheckout(Request $request){
        // todo
    }
    
}