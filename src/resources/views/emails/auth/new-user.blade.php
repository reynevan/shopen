@extends('shopen::layouts.mail')

@section('content')

    <!-- Nagłówek sekcji -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 16px 0; font-family:Arial, sans-serif; font-size:20px; line-height:28px; color:#1a1a1a; font-weight:bold;">
                Witamy w {{ config('app.name') }}!
            </td>
        </tr>
    </table>

    <!-- Krótkie powitanie -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 24px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                Cześć {{ $user->first_name }}!<br/><br/>
                Dziękujemy za rejestrację w naszym sklepie. Cieszymy się, że do nas dołączyłeś!
            </td>
        </tr>
    </table>

    <!-- Treść główna (ramka z akcentem) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#ffffff; border:2px solid #1f1f1f;">
        <tr>
            <td style="padding:20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                <strong>Twoje konto zostało pomyślnie utworzone!</strong><br/><br/>
                Od teraz możesz korzystać ze wszystkich funkcji naszego sklepu:<br/>
                <ul style="margin:12px 0; padding-left:20px;">
                    <li style="margin-bottom:8px;">Szybkie składanie zamówień</li>
                    <li style="margin-bottom:8px;">Śledzenie statusu przesyłek</li>
                    <li style="margin-bottom:8px;">Historia zakupów</li>
                    <li style="margin-bottom:8px;">Zapisane adresy dostawy</li>
                </ul>
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

    <!-- Dane konta -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280; font-weight:bold;">
                Dane Twojego konta:
            </td>
        </tr>
    </table>

    <!-- Ramka z danymi konta -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#f9fafb; border:1px solid #E5E7EB; border-left:4px solid #9ca3af;">
        <tr>
            <td style="padding:16px 20px; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#6b7280;">
                <strong style="color:#1a1a1a;">Imię i nazwisko:</strong> {{ $user->first_name }} {{ $user->last_name }}<br/>
                <strong style="color:#1a1a1a;">Email:</strong> {{ $user->email }}
            </td>
        </tr>
    </table>

    @includeFirst(['emails.elements.contact', 'shopen::emails.elements.contact')

    <!-- Spacer końcowy -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="24" style="line-height:24px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

    <!-- Pozdrowienia -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                Miłych zakupów!<br/>
                <strong>Zespół {{ config('app.name') }}</strong>
            </td>
        </tr>
    </table>

@endsection