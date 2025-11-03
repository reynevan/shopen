@if ($comment)
@includeFirst(['emails.elements.space', 'shopen::emails.elements.space'])
<!-- Blok komentarza -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate; mso-table-lspace:0; mso-table-rspace:0; background-color:#ffffff;" class="border rounded">
    <tr>
        <td style="padding:20px; font-family:Arial, sans-serif; font-size:16px; line-height:24px; color:#1a1a1a;">
            {!! nl2br($comment) !!}
        </td>
    </tr>
</table>
@endif