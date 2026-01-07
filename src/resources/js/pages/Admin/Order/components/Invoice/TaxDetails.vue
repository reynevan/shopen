<script setup>
    defineProps(['invoice'])
</script>

<template>
    <table>
        <thead class="bg-panel py-2">
        <tr>
            <th class="uppercase text-sm font-normal"></th>
            <th class="uppercase text-sm font-normal text-left px-4 py-2 ">VAT</th>
            <th class="uppercase text-sm font-normal text-left px-4 py-2 ">Wartość netto</th>
            <th class="uppercase text-sm font-normal text-left px-4 py-2 ">Kwota VAT</th>
            <th class="uppercase text-sm font-normal text-left px-4 py-2 ">Wartośc brutto</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b" v-for="taxRate in invoice.tax_rates">
            <td class="px-4 py-2 text-left">W tym</td>
            <td class="px-4 py-2 text-right">{{ taxRate.rate }}%</td>
            <td class="px-4 py-2 text-right">{{ taxRate.amount_net }}</td>
            <td class="px-4 py-2 text-right">{{ taxRate.tax_amount }}</td>
            <td class="px-4 py-2 text-right">{{ taxRate.amount }}</td>
        </tr>
        <tr class="border-b font-semibold">
            <td class="px-4 py-2 left">Suma</td>
            <td class="px-4 py-2"></td>
            <td class="px-4 py-2 text-right">{{ invoice.total_net_amount }}</td>
            <td class="px-4 py-2 text-right">{{ invoice.tax_amount }}</td>
            <td class="px-4 py-2 text-right">{{ invoice.total_amount }}</td>
        </tr>
        </tbody>
    </table>
</template>