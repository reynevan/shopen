<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin: 15px 0;">
    <tr>
        <td align="left" valign="top">

            <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <!-- LEWA KOLUMNA - ADRES DOSTAWY -->
                    <td align="left" valign="top" width="48%" class="border rounded" style=" padding: 15px; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; color: #333333;">
                        <h4 style="margin-top: 0; margin-bottom: 10px; font-family: Arial, sans-serif; font-size: 18px; font-weight: bold;">Adres dostawy:</h4>

                        @php $shippingAddress = $order->shippingAddress @endphp

                        {{ $shippingAddress->first_name }} {{ $shippingAddress->last_name }}<br>
                        @if($shippingAddress->company)
                            {{ $shippingAddress->company }}<br>
                        @endif
                        {{ $shippingAddress->address_line }}<br>
                        {{ $shippingAddress->postal_code }} {{ $shippingAddress->city }}<br>
                        @if($shippingAddress->phone)
                            Tel: {{ $shippingAddress->phone }}
                        @endif
                    </td>

                    <!-- ODSTĘP MIĘDZY KOLUMNAMI -->
                    <td width="4%">&nbsp;</td>

                    <!-- PRAWA KOLUMNA - ADRES ROZLICZENIOWY -->
                    <td align="left" valign="top" width="48%" class="border rounded" style=" padding: 15px; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px; color: #333333;">
                        <h4 style="margin-top: 0; margin-bottom: 10px; font-family: Arial, sans-serif; font-size: 18px; font-weight: bold;">Adres rozliczeniowy:</h4>

                        @php $billingAddress = $order->billingAddress @endphp

                        {{ $billingAddress->first_name }} {{ $billingAddress->last_name }}<br>
                        @if($billingAddress->company)
                            {{ $billingAddress->company }}<br>
                        @endif
                        {{ $billingAddress->address_line }}<br>
                        {{ $billingAddress->postal_code }} {{ $billingAddress->city }}<br>
                        @if($billingAddress->phone)
                            Tel: {{ $billingAddress->phone }}
                        @endif
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>