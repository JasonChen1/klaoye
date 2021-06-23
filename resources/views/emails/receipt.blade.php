@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => 'https://klaoye.com'])
KLAOYE
@endcomponent
@endslot

<div class="text-center mb-4">
  <h1 class="mb-4 fw-light">KLAOYE RECEIPT</h1>
  <strong>Order No: </strong><span>{{$orderNo}}</span>
  <br>
</div>
<table class="table" style="width:100%;">
  <tr>
    <th><strong>Name</strong></th>
    <th><strong>No</strong></th>
    <th><strong>Quantity</strong></th>
    <th><strong>Unit Price</strong></th>
    <th><strong>Discount</strong></th>
    <th><strong>Sub Total</strong></th>
    <th><strong>Color</strong></th>
  </tr>
  @foreach ($products as $p)
  <tr>
    <td style="text-align: center;">
      {!!$p['name']!!}
    </td>
    <td style="text-align: center;">
      {{$p['code']}}
    </td>
    <td style="text-align: center;">
      {{$p['num']}}
    </td>
    <td style="text-align: center;">
      ${{$p['price']}}
    </td>
    <td style="text-align: center;">
      ${{$p['discount']}}
    </td>
    <td style="text-align: center;">
      ${{$p['subtotal']}}
    </td>
    <td style="text-align: center;">
      <div class="color-block" style="
            background: {{ $p['color_code'] ? $p['color_code'] : '#fff'}};
            margin: 0.2rem 0.2rem 0 0;
            border-radius: 50%;
            width: 25px;
            height: 25px;"></div>
    </td>
  </tr>
  @endforeach
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>Subtotal</strong></td>
    <td>${{$totals['subtotal']}}</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>Discount</strong></td>
    <td>${{$totals['discount_total']}}</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>Delivery</strong></td>
    <td>${{$totals['delivery_total']}}</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>Total</strong></td>
    <td>${{$totals['total']}}</td>
  </tr>
</table>
<div class="text-center mb-4">
  <strong>Shipping Address: </strong><span>{{$address}}</span>
  <br>
</div>
@slot('footer')
@component('mail::footer')
© 2021 KLAOYE. All rights reserved.
@endcomponent
@endslot
@endcomponent