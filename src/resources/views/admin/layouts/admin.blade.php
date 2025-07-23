<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Shopen Admin</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Styles / Scripts -->

    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])

    @stack('views')
</head>
<body>
<div id="app">
    <header>
    </header>
    @block('menu.sidebar')
    <div class="pl-[100px]">
        <main>
            @yield('content')
        </main>
    </div>
</div>
@stack('body-scripts')
</body>
</html>
