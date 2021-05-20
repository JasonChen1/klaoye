  
@extends('layouts.adminApp')

@section('content')
<div id="app">
    <app/>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ mix('js/admin.js') }}" defer></script>
@endsection

