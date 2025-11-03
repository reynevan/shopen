<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Zamówienie zostało złożone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0 auto;
            padding: 20px;
            background-color: #fdf2ef;
        }
        .header {
            background-color: #fff1ee;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .border {
            border: 1px solid #ddd;
        }
        .border-bottom {
            border-bottom: 1px solid #ddd;
        }
        .rounded {
        }
    </style>
    @yield('style')
</head>
<body>


<!-- Wrapper z tłem -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#fdf2ef" style="border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0; background-color:#fdf2ef;">
    <tr>
        <td align="center" style="padding:0; margin:0;">

            <!-- Główna kolumna 600px (biała) -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" bgcolor="#ffffff" style="width:600px; max-width:100%; border-collapse:collapse; background-color:#ffffff; mso-table-lspace:0; mso-table-rspace:0;">
                <tr>
                    <!-- Padding wewnętrzny głównej kolumny -->
                    <td style="padding:24px 24px 0 24px;">

                        <!-- Header z logo -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                            <tr>
                                <td align="center" style="padding:0;">
                                    <a href="{{ config('app.name') }}" target="_blank" style="text-decoration:none; border:0; outline:none; display:inline-block;">
                                        <!-- Obrazek logo -->
                                        <img
                                                src="{{ url('/img/mail/logo.png')  }}"
                                                width="196"
                                                height="71"
                                                alt="{{ config('app.name') }}"
                                                border="0"
                                                style="display:block; width:196px; height:71px; max-width:100%; border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic;"
                                        />
                                    </a>
                                </td>
                            </tr>
                            <!-- odstęp pod logo -->
                            <tr>
                                <td height="24" style="line-height:24px; font-size:0;">&nbsp;</td>
                            </tr>
                            <!-- cienka linia opcjonalna -->
                            <tr>
                                <td>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                        <tr>
                                            <td height="1" style="line-height:1px; font-size:0; background-color:#E5E7EB;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- większy odstęp po linii -->
                            <tr>
                                <td height="24" style="line-height:24px; font-size:0;">&nbsp;</td>
                            </tr>
                        </table>

                        <!-- Tutaj zaczyna się treść maila w białej kolumnie z paddingiem -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#111827; padding:0 0 24px 0;">

                                    @yield('content')

                                </td>
                            </tr>
                        </table>
                        <!-- Koniec treści głównej -->

                    </td>
                </tr>

                <!-- Dodatkowy padding dolny białej kolumny -->
                <tr>
                    <td>
                        @includeFirst(['emails.elements.footer', 'shopen::emails.elements.footer'])
                    </td>
                </tr>
            </table>
            <!-- Koniec białej kolumny -->

            <!-- Odstęp pod białą kolumną, nadal na tle #fdf2ef -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px; max-width:100%; border-collapse:collapse;">
                <tr>
                    <td height="24" style="line-height:24px; font-size:0;">&nbsp;</td>
                </tr>
            </table>

        </td>
    </tr>
</table>


</body>
</html>