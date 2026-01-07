<script setup>
    defineProps(['invoice'])
</script>

<template>
    <table>
        <thead class="bg-panel py-2">
        <tr>
            <th class="uppercase text-sm font-normal text-left px-4 py-2">Forma płatności</th>
            <th class="uppercase text-sm font-normal text-center px-4 py-2 ">Termin płatności</th>
            <th v-if="invoice.is_correction" class="uppercase text-sm font-normal text-right px-4 py-2">
                <span v-if="invoice.amount_to_return > 0">
                    Kwota do zwrotu
                </span>
                <span v-else>
                    Kwota do zapłaty
                </span>
            </th>
            <th v-else class="uppercase text-sm font-normal text-right px-4 py-2">Kwota do zapłaty</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b font-semibold">
            <td class="px-4 py-2 text-left">{{ invoice.payment_method_label }}</td>
            <td class="px-4 py-2 text-center">{{ invoice.payment_due_date }}</td>
            <td v-if="invoice.is_correction"  class="px-4 py-2 text-right">{{ invoice.left_to_pay_amount }}</td>
            <td v-else class="px-4 py-2 text-right">{{ invoice.total_amount }}</td>
        </tr>
        </tbody>
    </table>
</template>