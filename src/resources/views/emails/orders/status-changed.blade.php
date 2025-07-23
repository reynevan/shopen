<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status zamówienia zmieniony</title>
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
        .status-pending { background-color: #fff3cd; }
        .status-processing { background-color: #d1ecf1; }
        .status-shipped { background-color: #d4edda; }
        .status-delivered { background-color: #d1e7dd; }
        .status-cancelled { background-color: #f8d7da; }
        .status-refunded { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Status zamówienia został zmieniony</h1>
        <p>Informujemy o aktualizacji statusu Twojego zamówienia.</p>
    </div>

    <div class="status-change">
        <div class="order-number">
            Numer zamówienia: {{ $order->order_number }}
        </div>
        
        <div class="status-info status-{{ $order->status }}">
            <h3>Nowy status: 
                @switch($order->status)
                    @case('pending')
                        Oczekujące
                        @break
                    @case('processing')
                        W trakcie realizacji
                        @break
                    @case('shipped')
                        Wysłane
                        @break
                    @case('delivered')
                        Dostarczone
                        @break
                    @case('cancelled')
                        Anulowane
                        @break
                    @case('refunded')
                        Zwrócone
                        @break
                    @default
                        {{ $order->status }}
                @endswitch
            </h3>
            
            @if($order->status === 'shipped')
                <p>Twoje zamówienie zostało wysłane. Wkrótce otrzymasz informacje o śledzeniu przesyłki.</p>
            @elseif($order->status === 'delivered')
                <p>Twoje zamówienie zostało dostarczone. Dziękujemy za zakupy!</p>
            @elseif($order->status === 'processing')
                <p>Twoje zamówienie jest w trakcie realizacji. Przygotowujemy je do wysyłki.</p>
            @elseif($order->status === 'cancelled')
                <p>Twoje zamówienie zostało anulowane. W przypadku pytań skontaktuj się z obsługą klienta.</p>
            @elseif($order->status === 'refunded')
                <p>Zwrot za Twoje zamówienie został przetworzony.</p>
            @endif
        </div>

        <div style="margin-top: 20px;">
            <h4>Szczegóły zamówienia:</h4>
            <p><strong>Data złożenia:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p><strong>Wartość zamówienia:</strong> {{ number_format($order->total_amount, 2) }} zł</p>
            <p><strong>Metoda dostawy:</strong> {{ $order->shipping_method }}</p>
            <p><strong>Metoda płatności:</strong> {{ $order->payment_method }}</p>
        </div>

        @if($order->status === 'shipped' && $order->shipped_at)
            <div style="margin-top: 15px; padding: 15px; background-color: #d4edda; border-radius: 5px;">
                <h4>Informacje o wysyłce:</h4>
                <p><strong>Data wysyłki:</strong> {{ $order->shipped_at->format('d.m.Y H:i') }}</p>
            </div>
        @endif

        @if($order->status === 'delivered' && $order->delivered_at)
            <div style="margin-top: 15px; padding: 15px; background-color: #d1e7dd; border-radius: 5px;">
                <h4>Informacje o dostawie:</h4>
                <p><strong>Data dostawy:</strong> {{ $order->delivered_at->format('d.m.Y H:i') }}</p>
            </div>
        @endif

        @if($isGuestOrder)
            <div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
                <h4>Zamówienie gościa</h4>
                <p>To zamówienie zostało złożone bez rejestracji. Aby śledzić status zamówienia, skontaktuj się z obsługą klienta podając numer zamówienia: <strong>{{ $order->order_number }}</strong></p>
            </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
        <p>W przypadku pytań skontaktuj się z obsługą klienta.</p>
    </div>
</body>
</html> 