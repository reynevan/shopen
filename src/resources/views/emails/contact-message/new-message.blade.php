@extends('shopen::layouts.mail')

@section('content')

    <!-- Nagłówek sekcji -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 24px 0; font-family:Arial, sans-serif; font-size:20px; line-height:28px; color:#1a1a1a; font-weight:bold;">
                Nowa wiadomość z formularza kontaktowego
            </td>
        </tr>
    </table>

    <!-- Dane nadawcy (ramka) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#f9fafb; border:1px solid #E5E7EB; margin-bottom:20px;">
        <tr>
            <td style="padding:20px; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#1a1a1a;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                    <!-- Imię -->
                    <tr>
                        <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280;">
                            <strong style="color:#1a1a1a;">Imię:</strong><br/>
                            {{ $contactMessage->name }}
                        </td>
                    </tr>

                    <!-- Email -->
                    <tr>
                        <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280;">
                            <strong style="color:#1a1a1a;">Email:</strong><br/>
                            <a href="mailto:{{ $contactMessage->email }}" style="color:#2563eb; text-decoration:underline;">{{ $contactMessage->email }}</a>
                        </td>
                    </tr>

                    <!-- Telefon (opcjonalnie) -->
                    @if($contactMessage->phone)
                        <tr>
                            <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280;">
                                <strong style="color:#1a1a1a;">Telefon:</strong><br/>
                                <a href="tel:{{ $contactMessage->phone }}" style="color:#2563eb; text-decoration:underline;">{{ $contactMessage->phone }}</a>
                            </td>
                        </tr>
                    @endif

                    <!-- Temat -->
                    <tr>
                        <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280;">
                            <strong style="color:#1a1a1a;">Temat:</strong><br/>
                            {{ $contactMessage->subject }}
                        </td>
                    </tr>

                    <!-- Data wysłania -->
                    <tr>
                        <td style="padding:0; font-family:Arial, sans-serif; font-size:14px; line-height:20px; color:#6b7280;">
                            <strong style="color:#1a1a1a;">Data wysłania:</strong><br/>
                            {{ $contactMessage->created_at->format('d.m.Y H:i') }}
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

    <!-- Spacer zamiast margin-bottom -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="8" style="line-height:8px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

    <!-- Nagłówek treści wiadomości -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td style="padding:0 0 12px 0; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a; font-weight:bold;">
                Treść wiadomości:
            </td>
        </tr>
    </table>

    <!-- Treść wiadomości (ramka) -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           class="border rounded"
           style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#ffffff; border:1px solid #E5E7EB;">
        <tr>
            <td style="padding:20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
                {!! nl2br(e($contactMessage->message)) !!}
            </td>
        </tr>
    </table>

    <!-- Spacer końcowy -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0;">
        <tr>
            <td height="24" style="line-height:24px; font-size:0;">&nbsp;</td>
        </tr>
    </table>

    <!-- Przycisk szybkiej odpowiedzi (opcjonalnie) -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; margin:0 auto;">
        <tr>
            <td align="center" style="background-color:#1e1e1e;">
                <a href="{{ route('admin.contact-messages.show', $contactMessage->id) }}"
                   target="_blank"
                   style="display:inline-block; padding:12px 32px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#ffffff; text-decoration:none; font-weight:bold;">
                    Odpowiedz klientowi
                </a>
            </td>
        </tr>
    </table>

@endsection