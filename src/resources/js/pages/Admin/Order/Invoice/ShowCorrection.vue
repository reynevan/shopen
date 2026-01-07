<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import InvoiceItems from "../components/Invoice/InvoiceItems.vue";
import BillingAddress from "../components/Invoice/BillingAddress.vue";
import PaymentDetails from "../components/Invoice/PaymentDetails.vue";
import TaxDetails from "../components/Invoice/TaxDetails.vue";
import {Link} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    invoice: {type: Object, required: true},
})
</script>
<template>
    <ActionsPanel back-route="admin.orders.show" :backRouteParams="[props.invoice.order_id]">
        <template #title>
            <PageTitle>Faktura {{ invoice.invoice_number }}</PageTitle>
        </template>
        <ActionButton type="download" target="_blank" :href="invoice.file_url">Pobierz</ActionButton>
    </ActionsPanel>
    <div class="px-6 py-8">
        <div class="flex flex-col sm:flex-row gap-12">
            <div class="mb-6 px-4 flex justify-between items-center border-t border-b py-4 w-full sm:w-1/2 max-w-[750px]">
                <div class="py-2 text-4xl">Faktura <span v-if="invoice.is_correction">korygująca</span></div>
                <div>
                    <p class="font-semibold text-xs">Numer dokumentu: {{ invoice.invoice_number }}</p>
                    <p class="w-1/2 text-xs whitespace-nowrap">
                        Data wystawienia: {{ invoice.issue_date }}
                    </p>
                </div>
            </div>
            <div v-if="invoice.base_invoice"
                 class="mb-6 px-4 flex items-center justify-between border-t border-b py-4 w-full sm:w-1/2 max-w-[750px]">
                <div class="py-2 text-xl">Do faktury nr {{ invoice.base_invoice.invoice_number }}</div>
                <div>
                    <p class="w-1/2 text-xs whitespace-nowrap">
                        Data wystawienia: {{ invoice.base_invoice.issue_date }}
                    </p>
                    <p class="w-1/2 text-xs whitespace-nowrap">
                        Data sprzedaży: {{ invoice.order.placed_date }}
                    </p>
                </div>
            </div>
        </div>
        <div class="text-2xl mb-6 mt-4">
            Przed korektą
        </div>
        <div class="border-b pb-5 mb-4" v-if="invoice.base_invoice && invoice.is_address_corrected">
            <div class="font-semibold">Nabywca</div>
            <BillingAddress :address="invoice.base_invoice.billing_address"/>
        </div>
        <InvoiceItems v-if="invoice.has_items_corrected" :items="invoice.base_invoice.items"/>

        <div class="flex justify-between items-start gap-6 mt-6">
            <div class="w-full">
            </div>
            <div class="w-full flex justify-end">
                <TaxDetails :invoice="invoice.base_invoice"/>
            </div>
        </div>

        <div class="text-2xl mb-6 mt-12">
            Po korekcie
        </div>
        <div class="border-b pb-5 mb-4" v-if="invoice.is_address_corrected">
            <div class="font-semibold">Nabywca</div>
            <BillingAddress :address="invoice.billing_address"/>
        </div>
        <InvoiceItems v-if="invoice.has_items_corrected" :items="invoice.items"/>


        <div class="flex justify-between items-start gap-6 mt-6">
            <div class="w-full">
                <PaymentDetails :invoice="invoice"/>
            </div>
            <div class="w-full flex justify-end">
                <TaxDetails :invoice="invoice"/>
            </div>
        </div>
    </div>
</template>