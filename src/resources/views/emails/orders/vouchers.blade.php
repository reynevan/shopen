<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kody podarunkowe</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }

        .status-change {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }

        .order-number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .status-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<table style="margin-bottom:16px;">
    <tr><td>Dziękujemy za dokonanie zakupu w naszym sklepie.</td></tr>
    <tr><td>Poniżej przesyłamy szczegóły Twojego bonu podarunkowego.</td></tr>
</table>

@foreach ($vouchers as $voucher)
    <table>
        <tr>
            <td>
                Produkt: <span style="font-weight: 600">{{ $voucher['name'] }}</span>
            </td>
        </tr>
    </table>
    <table style="margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #bbb">
        <tr>
            <td>
                Kod bonu:
            </td>
            @foreach ($voucher['codes'] as $code)
                <td style="padding: 5px 10px">
                    <table style="border: 1px solid #333; padding: 4px 8px; font-size: 12px;">
                        <tr>
                            <td>
                                {{ $code }}
                            </td>
                        </tr>
                    </table>
                </td>
            @endforeach
        </tr>
    </table>
@endforeach
<table style="margin-top: 16px;">
    <tr>
        <td style="font-weight: 600">
            Jak skorzystać z bonu:
        </td>
    </tr>
    <tr>
        <td>
            1. Dodaj produkty do koszyka w naszym sklepie.
        </td>
    </tr>
    <tr>
        <td>
            2. Przejdź do kasy.
        </td>
    </tr>
    <tr>
        <td>
            3. Wprowadź kod bonu w polu „Kod rabatowy” i zatwierdź.
        </td>
    </tr>
    <tr>
        <td>
            4. Zniżka zostanie naliczona automatycznie.
        </td>
    </tr>
</table>

<div style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
    <p>W przypadku pytań skontaktuj się z obsługą klienta.</p>
</div>
</body>
</html>