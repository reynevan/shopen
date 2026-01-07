@php
    use Illuminate\Support\Number;
@endphp
<table style="width: 100%; margin-bottom: 16px;" class="items">
    <thead style="padding: 8px 0;">
    <tr>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Lp</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: left" class="text-xs">Nazwa towaru/usługi</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Ilość</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Jednostka miary</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Cena jedn. netto</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Rabat netto</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Stawka VAT</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Kwota VAT</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Wartość netto</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Wartość brutto</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoice->items as $i => $item)
        <tr class="border-b hover:bg-accent/50 transition-colors">
            <td style="text-align: right; padding: 2px 4px;" class="text-xs text-left">{{ $i + 1 }}</td>
            <td style="text-align: left; padding: 2px 4px;" class="text-xs">
                <div class="flex items-start pl-4 gap-4">
                    <div>
                        <div>{{ $item->name }}</div>
                        @if ($item->sku)
                            <div class="mt-1 text-neutral-500 text-xs">kod: {{ $item->sku }}</div>
                        @endif
                    </div>
                </div>
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ $item->quantity }}
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ $item->unit }}
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ Number::currency($item->price_net) }}
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ Number::currency($item->discount_amount_net) }}
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ $item->tax_rate }}%
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ Number::currency($item->tax_amount) }}
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ Number::currency($item->total_net) }}
            </td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">
                {{ Number::currency($item->total) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>