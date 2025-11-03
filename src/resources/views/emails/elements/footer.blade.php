<!-- Stopka -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#1e1e1e" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0; background-color:#1e1e1e;">
    <tr>
        <td style="padding:32px 24px;">

            <!-- Logo / nazwa sklepu -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:0 0 16px 0;">
                        <img
                            src="{{ url('/img/mail/logo-light.png')  }}"
                            width="196"
                            height="71"
                            alt="{{ config('app.name') }}"
                            border="0"
                            style="display:block; width:196px; height:71px; max-width:100%; border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic;"
                            />
                    </td>
                </tr>
            </table>

            <!-- Linki nawigacyjne -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:0 0 20px 0; font-family:Arial, sans-serif; font-size:14px; line-height:20px;">
                        <a href="{{ url('/kontakt') }}" target="_blank" style="color:#ffffff; text-decoration:none; padding:0 12px;">Kontakt</a>
                        <span style="color:#6b7280;">|</span>
                        <a href="{{ url('/pomoc') }}" target="_blank" style="color:#ffffff; text-decoration:none; padding:0 12px;">Pomoc</a>
                        <span style="color:#6b7280;">|</span>
                        <a href="{{ url('/regulamin') }}" target="_blank" style="color:#ffffff; text-decoration:none; padding:0 12px;">Regulamin</a>
                        <span style="color:#6b7280;">|</span>
                        <a href="{{ url('/polityka-prywatnosci') }}" target="_blank" style="color:#ffffff; text-decoration:none; padding:0 12px;">Prywatność</a>
                    </td>
                </tr>
            </table>

            <!-- Social media ikony (opcjonalnie) -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse; margin:0 auto;">
                <tr>
                    <td style="padding:0 8px;">
                        <a href="https://facebook.com/" target="_blank" style="text-decoration:none; border:0; outline:none;">
                            <img src="{{ url('/img/mail/facebook.png') }}" width="32" height="32" alt="Facebook" border="0" style="display:block; width:32px; height:32px; border:0; -ms-interpolation-mode:bicubic;" />
                        </a>
                    </td>
                    <td style="padding:0 8px;">
                        <a href="https://instagram.com/" target="_blank" style="text-decoration:none; border:0; outline:none;">
                            <img src="{{ url('/img/mail/instagram.png') }}" width="32" height="32" alt="Instagram" border="0" style="display:block; width:32px; height:32px; border:0; -ms-interpolation-mode:bicubic;" />
                        </a>
                    </td>
                    <td style="padding:0 8px;">
                        <a href="tel:+48123456789" target="_blank" style="text-decoration:none; border:0; outline:none;">
                            <img src="{{ url('/img/mail/whatsapp.png') }}" width="32" height="32" alt="Whatsapp" border="0" style="display:block; width:32px; height:32px; border:0; -ms-interpolation-mode:bicubic;" />
                        </a>
                    </td>
                </tr>
            </table>

            <!-- Spacer -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td height="20" style="line-height:20px; font-size:0;">&nbsp;</td>
                </tr>
            </table>

            <!-- Dane firmy -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:0; font-family:Arial, sans-serif; font-size:13px; line-height:20px; color:#9ca3af;">
                        TwojSklep.pl Sp. z o.o.<br/>
                        ul. Przykładowa 123, 00-001 Warszawa<br/>
                        NIP: 123-456-78-90 | KRS: 0000123456<br/>
                        <a href="mailto:{{ config('shopen.contact_email') }}" style="color:#9ca3af; text-decoration:underline;">{{ config('shopen.contact_email') }}</a> | tel. +48 123 456 789
                    </td>
                </tr>
            </table>

            <!-- Spacer -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td height="16" style="line-height:16px; font-size:0;">&nbsp;</td>
                </tr>
            </table>

            <!-- Informacja o wiadomości automatycznej -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:0; font-family:Arial, sans-serif; font-size:12px; line-height:18px; color:#6b7280;">
                        Ta wiadomość została wygenerowana automatycznie. Prosimy nie odpowiadać na ten email.<br/>
                        Jeśli masz pytania, <a href="{{ url('/kontakt') }}" target="_blank" style="color:#9ca3af; text-decoration:underline;">skontaktuj się z nami</a>.
                    </td>
                </tr>
            </table>

            <!-- Spacer -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td height="16" style="line-height:16px; font-size:0;">&nbsp;</td>
                </tr>
            </table>

            <!-- Link do wypisania się (opcjonalnie dla newsletterów) -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:0; font-family:Arial, sans-serif; font-size:12px; line-height:18px; color:#6b7280;">
                        © {{ date('Y') }} {{ config('app.name') }}. Wszelkie prawa zastrzeżone.
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>