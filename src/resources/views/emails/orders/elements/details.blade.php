<table style="margin-top: 20px;">
    <tbody>
    <tr>
        <td>
            <span style="color:#1a1a1a;font-weight:bold;font-size:18px;">Szczegóły zamówienia:</span>
        </td>
    </tr>
    </tbody>
</table>

<table width="100%" class="border rounded" style="padding:10px 5px">
    <tbody>
    <tr>
        <td width="50%">
            <table><tbody><tr><td><span style="font-weight:bold">Metoda dostawy:</span></td></tr></tbody></table>
            <table><tbody><tr><td>{{ $order->getShippingMethodName() }}</td></tr></tbody></table>
        </td>
        <td width="50%">
            <table><tbody><tr><td><span style="font-weight:bold">Płatność:</span></td></tr></tbody></table>
            <table><tbody><tr><td>{{ $order->getPaymentMethodName() }}</td></tr></tbody></table>
        </td>
    </tr>
    <tr>
        <td>

            <table><tbody><tr><td><span style="font-weight:bold">Numer zamówienia:</span></td></tr></tbody></table>
            <table><tbody><tr><td>{{ $order->order_number }}</td></tr></tbody></table>
        </td>
    </tr>
    </tbody>
</table>