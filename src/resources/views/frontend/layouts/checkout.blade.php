<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->

    <!-- Styles / Scripts -->

    @vite(['resources/css/app.css'])

    @stack('head')

</head>
<body class="min-h-screen">
<div id="app">
    <div class="page-wrapper relative transition-[left] duration-500 left-0">
        <div class="py-6">
            Logo
            <a href="/">Kontunuuj zakupu</a>
        </div>
        <div class="bg-primary-bg">
            <div class="container mx-auto max-w-7xl">
                czekałt layout
                <main>
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    <cover></cover>
    @block('init.cart')
</div>
<script>
    window.user = {!! json_encode($user ?? Auth::user()) !!};
</script>
@vite(['resources/js/frontend/app.js'])
</body>
</html>
