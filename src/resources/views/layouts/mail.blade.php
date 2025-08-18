<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Zamówienie zostało złożone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #fff1ee;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .border {
            border: 1px solid #ddd;
        }
        .border-bottom {
            border-bottom: 1px solid #ddd;
        }
        .rounded {
            border-radius: 12px;
        }
    </style>
    @yield('style')
</head>
<body>

<img src="{{ $message->embed($logoPath) }}"
     alt="Logo"
     width="140"
     style="display:block; max-width:140px; height:auto;">

@yield('content')

<div style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
    <p>Dziękujemy za zakupy w naszym sklepie!</p>
</div>
</body>
</html>