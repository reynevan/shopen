@extends('shopen::layouts.mail')

@section('content')

    <!-- Nagłówek sekcji -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 16px 0; font-family:Arial, sans-serif; font-size:20px; line-height:28px; color:#1a1a1a; font-weight:bold;">
                Odpowiedź na Twoją wiadomość
            </td>
        </tr>
    </table>

    <!-- Krótkie powitanie -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 24px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                Dziękujemy za kontakt! Poniżej znajdziesz odpowiedź na Twoją wiadomość.
            </td>
        </tr>
    </table>

    <!-- Treść odpowiedzi (główna ramka z akcentem) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#ffffff; border:2px solid #1f1f1f;">
        <tr>
            <td style="padding:20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                {!! nl2br(e($response->message)) !!}
            </td>
        </tr>
    </table>

    <!-- Spacer -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="32" style="line-height:32px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

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

    <!-- Nagłówek oryginalnej wiadomości -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280; font-weight:bold;">
                Twoja wiadomość z dnia {{ $contactMessage->created_at->format('d.m.Y') }}:
            </td>
        </tr>
    </table>

    <!-- Oryginalna wiadomość (cytowana, jasnoszare tło) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#f9fafb; border:1px solid #E5E7EB; border-left:4px solid #9ca3af;">
        <tr>
            <td style="padding:16px 20px; font-family:Arial, sans-serif; font-size:14px; line-height:22px; color:#6b7280;">
                {!! nl2br(e($contactMessage->message)) !!}
            </td>
        </tr>
    </table>


    @includeFirst(['emails.elements.contact', 'shopen::emails.elements.contact'])

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
                Pozdrawiamy,<br/>
                <strong>Zespół {{ config('app.name') }}</strong>
            </td>
        </tr>
    </table>

@endsection