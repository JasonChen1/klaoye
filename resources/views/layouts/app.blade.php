<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head')
</head>
<body id="body" class="antialiased">
    @yield('header')
    @yield('content')
    
    @yield('scripts')
</body>
</html>

