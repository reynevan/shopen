<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Styles / Scripts -->

    @vite(['resources/css/app.css'])

    @stack('views')
</head>
<body class="overflow-x-hidden">
<div id="app" class="overflow-x-hidden">
    <div class="page-wrapper relative transition-[left] ease-in-out duration-500 left-0">

        @block('header')
        <div class="container mx-auto max-w-7xl">
            @block('layout.breadcrumbs')
            main layout
            <main>
                @yield('content')
            </main>
        </div>
        @block('footer')
    </div>
    <cover></cover>
    @block('init.cart')
</div>
<script>
    window.user = {!! json_encode(Auth::user()) !!};
</script>
@vite(['resources/js/frontend/app.js'])
</body>
</html>
