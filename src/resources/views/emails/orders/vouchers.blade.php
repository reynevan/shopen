@extends('shopen::layouts.mail')

@section('content')

    <!-- Nagłówek sekcji -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 16px 0; font-family:Arial, sans-serif; font-size:20px; line-height:28px; color:#1a1a1a; font-weight:bold;">
                Twoje bony podarunkowe są gotowe!
            </td>
        </tr>
    </table>

    <!-- Wprowadzenie -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 8px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                Dziękujemy za zakup w naszym sklepie!
            </td>
        </tr>
        <tr>
            <td style="padding:0 0 24px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                Poniżej znajdziesz kody bonów podarunkowych, które możesz wykorzystać podczas kolejnych zakupów lub podarować bliskim.
            </td>
        </tr>
    </table>

    <!-- Pętla po bonach -->
    @foreach ($vouchers as $voucher)

        <!-- Nazwa produktu (bon) -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
            <tr>
                <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                    Bon podarunkowy: <strong>{{ $voucher['name'] }}</strong>
                </td>
            </tr>
        </table>

        <!-- Kody bonów -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
            <tr>
                <td style="padding:0 0 8px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280;">
                    @if(count($voucher['codes']) > 1)
                        Kody bonów:
                    @else
                        Kod bonu:
                    @endif
                </td>
            </tr>
        </table>

        <!-- Tabela z kodami (w jednym wierszu lub kolumnie w zależności od liczby) -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
            <tr>
                <td style="padding:0 0 20px 0;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                        @foreach ($voucher['codes'] as $code)
                            <tr>
                                <td style="padding:0 12px 12px 0;">
                                    <!-- Ramka z kodem -->
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                           style="border-collapse:separate; border:1px solid #000; background-color:#fff;">
                                        <tr>
                                            <td style="padding:12px 20px; font-family:'Courier New', monospace; font-size:18px; line-height:24px; color:#000; font-weight:bold; letter-spacing:1px;">
                                                {{ $code }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

        <!-- Separator między bonami -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
            <tr>
                <td style="padding:0 0 20px 0;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                        <tr>
                            <td height="1" style="line-height:1px; font-size:0; background-color:#E5E7EB;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @endforeach

    <!-- Spacer przed instrukcją -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="8" style="line-height:8px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

    <!-- Instrukcja użycia (ramka z ikoną) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#f0fdf4; border:1px solid #86efac;">
        <tr>
            <td style="padding:20px; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#166534;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#15803d; font-weight:bold;">
                            💡 Jak skorzystać z bonu podarunkowego?
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 8px 0; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#166534;">
                            <strong>1.</strong> Dodaj produkty do koszyka w naszym sklepie
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 8px 0; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#166534;">
                            <strong>2.</strong> Przejdź do kasy i rozpocznij finalizację zamówienia
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 8px 0; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#166534;">
                            <strong>3.</strong> Wprowadź kod bonu w polu „Kod rabatowy" lub „Bon podarunkowy"
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#166534;">
                            <strong>4.</strong> Zatwierdź kod – zniżka zostanie naliczona automatycznie
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- Spacer -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="24" style="line-height:24px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

    <!-- Przycisk CTA do sklepu -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; margin:0 auto;">
        <tr>
            <td align="center" style="background-color:#1f1f1f;">
                <a href="{{ url('/') }}"
                   target="_blank"
                   style="display:inline-block; padding:14px 32px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#ffffff; text-decoration:none; font-weight:bold; border-radius:6px;">
                    Przejdź do sklepu
                </a>
            </td>
        </tr>
    </table>

    <!-- Spacer -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="32" style="line-height:32px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

    <!-- Informacja o pomocy -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#6b7280; text-align:center;">
                Masz pytania dotyczące bonów? <a href="{{ url('/kontakt') }}" target="_blank" style="color:#2563eb; text-decoration:underline;">Skontaktuj się z nami</a> – chętnie pomożemy!
            </td>
        </tr>
    </table>

@endsection