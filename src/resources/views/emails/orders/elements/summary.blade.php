<!-- Blok podsumowania kwot (wyrównany do prawej) -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
    <tr>
        <td align="right" style="padding:0;">

            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td align="right" style="padding:0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                        Wartość produktów: {{ Number::currency($order->subtotal) }}
                    </td>
                </tr>
                <tr>
                    <td align="right" style="padding:0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                        Dostawa: {{ Number::currency($order->shipping_amount) }}
                    </td>
                </tr>
                @if($order->payment_amount > 0)
                    <tr>
                        <td align="right" style="padding:0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                            Koszt płatności: {{ Number::currency($order->payment_amount) }}
                        </td>
                    </tr>
                @endif

                <!-- Spacer zamiast margin-top -->
                <tr>
                    <td height="10" style="line-height:10px; font-size:0;">&nbsp;</td>
                </tr>

                <tr>
                    <td align="right" style="padding:0; font-family:Arial, sans-serif; font-size:18px; line-height:24px; color:#1a1a1a;">
                        <strong>Razem: {{ Number::currency($order->total_amount) }}</strong>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>