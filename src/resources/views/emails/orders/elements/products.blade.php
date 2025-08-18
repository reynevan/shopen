<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="border rounded" style="margin-top: 20px; margin-bottom: 20px; background-color: #ffffff;">
    <tr>
        <td style="">

            {{-- Pętla po produktach z zamówienia --}}
            @foreach($items as $i => $item)
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;" class="@if($i < count($items) - 1) border-bottom @endif">
                    <tr>
                        <td style="padding: 15px 20px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <tr>
                                    {{-- Kolumna z obrazkiem --}}
                                    <td width="65" valign="top" style="width: 65px; padding-right: 15px;">
                                        <img
                                                src="{{ $item->product->getMailThumbnailUrl() }}"
                                                alt="{{ $item->name }}"
                                                width="65"
                                                height="65"
                                                style="display: block; width: 65px; height: 65px; border: 0;"
                                        />
                                    </td>

                                    {{-- Kolumna z danymi produktu --}}
                                    <td valign="top">
                                        {{-- Nazwa produktu --}}
                                        <div style="font-size: 18px; font-weight: bold; color: #333333; margin-bottom: 8px;">
                                            {{ $item->name }}
                                        </div>

                                        {{-- Tabela z ilością i ceną --}}
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                            <tr>
                                                {{-- Ilość --}}
                                                <td align="left" style="font-size: 14px; color: #555555;">
                                                    {{ $item->quantity }} szt.
                                                </td>
                                                {{-- Cena --}}
                                                <td align="right" style="font-size: 14px; color: #333333;">
                                                    {{ Number::currency($item->total) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            @endforeach
            {{-- Koniec pętli po produktach --}}

        </td>
    </tr>
</table>