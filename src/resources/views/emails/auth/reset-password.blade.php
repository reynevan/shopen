@extends('shopen::layouts.mail')

@section('content')

    <!-- Krótkie powitanie -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 24px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                Cześć {{ $user->first_name }}!<br/><br/>
                Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta w {{ config('app.name') }}.
            </td>
        </tr>
    </table>

    <!-- Treść główna (ramka z akcentem) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#ffffff; border:2px solid #1f1f1f;">
        <tr>
            <td style="padding:20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a; text-align:center;">
                Aby ustawić nowe hasło, kliknij w poniższy przycisk:<br/><br/>

                <!-- Przycisk CTA -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; margin:0 auto;">
                    <tr>
                        <td style="padding:12px 32px; background-color:#1f1f1f; text-align:center; border-radius:4px;">
                            <a href="{{ $resetUrl }}" target="_blank" style="font-family:Arial, sans-serif; font-size:16px; font-weight:bold; color:#ffffff; text-decoration:none; display:inline-block;">
                                Zresetuj hasło
                            </a>
                        </td>
                    </tr>
                </table>

                <br/><br/>
                Lub skopiuj i wklej poniższy link do przeglądarki:<br/>
                <a href="{{ $resetUrl }}" target="_blank" style="color:#1f1f1f; text-decoration:underline; word-break:break-all;">{{ $resetUrl }}</a>
            </td>
        </tr>
    </table>

    @includeFirst(['emails.elements.space', 'shopen::emails.elements.space'])

    <!-- Separator wizualny -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 16px 0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    <tr>
                        <td height="1" style="line-height:1px; font-size:0; background-color:#E5E7EB;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Informacja o ważności linku -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280; font-weight:bold;">
                Ważne informacje:
            </td>
        </tr>
    </table>

    <!-- Ramka z informacjami -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#f9fafb; border:1px solid #E5E7EB; border-left:4px solid #9ca3af;">
        <tr>
            <td style="padding:16px 20px; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#6b7280;">
                • Link do resetowania hasła jest ważny przez <strong style="color:#1a1a1a;">60 minut</strong><br/>
                • Po tym czasie będziesz musiał ponownie zażądać resetu hasła<br/>
            </td>
        </tr>
    </table>

    @includeFirst(['emails.elements.space', 'shopen::emails.elements.space'])

    <!-- Sekcja ostrzeżenia bezpieczeństwa -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#fef3c7;">
        <tr>
            <td style="padding:16px 20px; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#92400e;">
                <strong style="color:#78350f;">Nie prosiłeś o reset hasła?</strong><br/>
                Jeśli nie składałeś tej prośby, skontaktuj się z nami natychmiast przez
                <a href="{{ url('/kontakt') }}" target="_blank" style="color:#92400e; text-decoration:underline;">formularz kontaktowy</a>.
                Twoje konto może być zagrożone.
            </td>
        </tr>
    </table>

@endsection