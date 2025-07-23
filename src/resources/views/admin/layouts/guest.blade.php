<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shopen Admin</title>

    <!-- Fonts -->

    <!-- Styles / Scripts -->

    @vite(['resources/css/admin.css', 'resources/js/admin/app.js'])

    @stack('views')
</head>
<body>
<header>
</header>
    admin guest layout
    <main>
        @yield('content')
    </main>
</body>
</html>
