<table style="margin-top: 15px;">
    <tbody>
    <tr>
        <td style="font-size: 18px;">
            Witaj, @if($order->user && $order->user->first_name) {{ $order->user->first_name }}, @endif
        </td>
    </tr>
    </tbody>
</table>