  
@extends('layouts.app')

@section('content')
<div id="app">
{!! $ssr !!}
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ mix('js/shop-client.js') }}" ></script>
@endsection
