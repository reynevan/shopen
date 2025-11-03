@includeFirst(['emails.elements.space', 'shopen::emails.elements.space'])

<!-- Nagłówek sekcji -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
    <tr>
        <td style="padding:0;">
            <span style="font-family:Arial, sans-serif; color:#1a1a1a; font-weight:bold; font-size:18px; line-height:24px;">
                Szczegóły zamówienia:
            </span>
        </td>
    </tr>
</table>

<!-- Główna tabela z ramką i zaokrągleniami (klasy zostawione) -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="border rounded" style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#ffffff;">
    <tr>
        <!-- Padding przeniesiony na komórki (email-safe) -->
        <td width="50%" valign="top" style="padding:20px 5px 10px 20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0 0 4px 0;">
                        <span style="font-weight:bold;">Metoda dostawy:</span>
                    </td>
                </tr>
            </table>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0;">
                        <span style="font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                          {{ $order->getShippingMethodName() }}
                        </span>
                    </td>
                </tr>
            </table>
        </td>

        <td width="50%" valign="top" style="padding:20px 20px 5px 5px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0 0 4px 0;">
                        <span style="font-weight:bold;">Płatność:</span>
                    </td>
                </tr>
            </table>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0;">
                        <span style="font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                          {{ $order->getPaymentMethodName() }}
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2" valign="top" style="padding:10px 5px 10px 20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0 0 4px 0;">
                        <span style="font-weight:bold;">Numer zamówienia:</span>
                    </td>
                </tr>
            </table>
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td style="padding:0;">
                        <span style="font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                          {{ $order->order_number }}
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>