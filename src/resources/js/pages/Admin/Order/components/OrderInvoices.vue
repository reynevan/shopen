<script setup>
    import ActionButton from "../../../../components/admin/ui/ActionButton.vue";
    import ActionButtons from "../../../../components/admin/ui/ActionButtons.vue";

    defineProps({
        invoices: Array
    })
</script>

<template>
    <table class="w-full">
        <thead class="bg-panel py-2">
        <tr class="border-b">
            <th class="uppercase text-sm font-normal text-left pr-4 pl-2 py-2">Numer</th>
            <th class="uppercase text-sm font-normal text-right pr-4 py-2 w-[200px]">Data wystawienia</th>
            <th class="uppercase text-sm font-normal text-right pr-4 py-2 w-[150px]">Kwota</th>
            <th class="uppercase text-sm font-normal text-right pr-4 py-2 w-[50px]">Akcje</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b hover:bg-accent/50 transition-colors" v-for="invoice in invoices">
            <td class="pr-4 pl-2 py-2">{{ invoice.invoice_number }}</td>
            <td class="px-4 py-2 text-right">{{ invoice.created_at }}</td>
            <td class="px-4 py-2 text-right">{{ invoice.total_amount }}</td>
            <td class="px-4 py-2">
                <div class="flex justify-end">
                    <ActionButtons>
                        <ActionButton type="view" :href="route('admin.orders.invoices.show', invoice.id)"/>
                        <ActionButton type="download" target="_blank" :href="invoice.file_url"/>
                    </ActionButtons>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>