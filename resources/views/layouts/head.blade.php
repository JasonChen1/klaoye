<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>KLAOYE</title>

<!-- Fonts -->
<link rel="shortcut icon" type="image/jpg" href="/images/favicon.ico"/>
<link href="{{ mix('css/app.css') }}" rel="stylesheet" async>

<!-- script -->
<script src="https://js.stripe.com/v3/"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>