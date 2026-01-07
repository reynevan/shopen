<script setup>
    defineProps(['items'])
</script>

<template>
    <table class="w-full">
        <thead class="bg-panel py-2">
        <tr>
            <th class="uppercase text-sm font-normal w-[35px] text-left px-4 py-2 ">Lp</th>
            <th class="uppercase text-sm font-normal text-left px-4 py-2 ">Nazwa towaru/usługi</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Ilość</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Jednostka miary</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Cena jedn. netto</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Rabat netto</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Stawka VAT</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Kwota VAT</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Wartość netto</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Wartość brutto</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b hover:bg-accent/50 transition-colors" v-for="(item, i) in items">
            <td class="px-4 py-2 text-left">{{ i + 1 }}</td>
            <td class="px-4 py-2">
                <div class="flex items-start pl-4 gap-4">
                    <div>
                        <div>{{ item.name }}</div>
                        <div v-if="item.sku" class="mt-1 text-neutral-500 text-sm">SKU: {{ item.sku }}</div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-2 text-right">{{ item.quantity }}</td>
            <td class="px-4 py-2 text-right">{{ item.unit }}</td>
            <td class="px-4 py-2 text-right">{{ $currency(item.price_net) }}</td>
            <td class="px-4 py-2 text-right">{{ $currency(item.discount_amount_net) }}</td>
            <td class="px-4 py-2 text-right">{{ item.tax_rate }}%</td>
            <td class="px-4 py-2 text-right">{{ $currency(item.tax_amount) }}</td>
            <td class="px-4 py-2 text-right">{{ $currency(item.total_net) }}</td>
            <td class="px-4 py-2 text-right">{{ $currency(item.total) }}</td>
        </tr>
        </tbody>
    </table>
</template>>