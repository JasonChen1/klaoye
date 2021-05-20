<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.adminHead')
</head>
<body id="body" class="antialiased">
    @yield('header')
    @yield('content')
    <script type="text/javascript" src="{{ asset('js/manifest.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/vendor.js') }}" defer></script>
    @yield('scripts')
</body>
</html>

