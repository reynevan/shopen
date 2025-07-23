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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .order-details {
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
        .items {
            margin: 20px 0;
        }
        .item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
            text-align: right;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #007bff;
        }
        .address {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dziękujemy za zamówienie!</h1>
        <p>Twoje zamówienie zostało złożone pomyślnie.</p>
    </div>

    <div class="order-details">
        <div class="order-number">
            Numer zamówienia: {{ $order->order_number }}
        </div>
        
        <div class="items">
            <h3>Zamówione produkty:</h3>
            @foreach($order->items as $item)
                <div class="item">
                    <strong>{{ $item->name }}</strong><br>
                    Ilość: {{ $item->quantity }} × {{ number_format($item->price, 2) }} zł = {{ number_format($item->total, 2) }} zł
                </div>
            @endforeach
        </div>

        <div class="total">
            <div>Wartość produktów: {{ number_format($order->subtotal, 2) }} zł</div>
            <div>Dostawa: {{ number_format($order->shipping_amount, 2) }} zł</div>
            <div style="font-size: 18px; margin-top: 10px;">
                <strong>Do zapłaty: {{ number_format($order->total_amount, 2) }} zł</strong>
            </div>
        </div>

        <div class="address">
            <h4>Adres dostawy:</h4>
            @php $shippingAddress = $order->shippingAddress() @endphp
            <div>
                {{ $shippingAddress->first_name }} {{ $shippingAddress->last_name }}<br>
                @if($shippingAddress->company)
                    {{ $shippingAddress->company }}<br>
                @endif
                {{ $shippingAddress->address_line }}<br>
                {{ $shippingAddress->postal_code }} {{ $shippingAddress->city }}<br>
                @if($shippingAddress->phone)
                    Tel: {{ $shippingAddress->phone }}<br>
                @endif
            </div>
        </div>

        @if($order->billingAddress() && $order->billingAddress()->id !== $order->shippingAddress()->id)
            <div class="address">
                <h4>Adres rozliczeniowy:</h4>
                @php $billingAddress = $order->billingAddress() @endphp
                <div>
                    {{ $billingAddress->first_name }} {{ $billingAddress->last_name }}<br>
                    @if($billingAddress->company)
                        {{ $billingAddress->company }}<br>
                    @endif
                    {{ $billingAddress->address_line }}<br>
                    {{ $billingAddress->postal_code }} {{ $billingAddress->city }}<br>
                    @if($billingAddress->phone)
                        Tel: {{ $billingAddress->phone }}<br>
                    @endif
                </div>
            </div>
        @endif

        <div style="margin-top: 20px; padding: 15px; background-color: #e7f3ff; border-radius: 5px;">
            <h4>Informacje dodatkowe:</h4>
            <p><strong>Metoda dostawy:</strong> {{ $order->shipping_method }}</p>
            <p><strong>Metoda płatności:</strong> {{ $order->payment_method }}</p>
            <p><strong>Status:</strong> Oczekujące</p>
        </div>

        @if($isGuestOrder)
            <div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
                <h4>Zamówienie gościa</h4>
                <p>To zamówienie zostało złożone bez rejestracji. Aby śledzić status zamówienia, skontaktuj się z obsługą klienta podając numer zamówienia: <strong>{{ $order->order_number }}</strong></p>
            </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
        <p>Dziękujemy za zakupy w naszym sklepie!</p>
        <p>W przypadku pytań skontaktuj się z obsługą klienta.</p>
    </div>
</body>
</html> 