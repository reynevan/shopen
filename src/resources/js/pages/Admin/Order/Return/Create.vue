<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "../../../../components/admin/ui/ActionsPanel.vue";
import PageTitle from "../../../../components/admin/ui/PageTitle.vue";
import Panel from "../../../../components/admin/ui/Panel.vue";
import OrderDetails from "../components/OrderDetails.vue";
import EditReturnItems from "../components/Return/EditReturnItems.vue";
import {useForm} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";
import Input from "../../../../components/admin/form/input/Input.vue";
import Button from "../../../../components/admin/ui/Button.vue";
import FormField from "../../../../components/admin/form/FormField.vue";
import Toggle from "../../../../components/admin/form/input/Toggle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    order: {type: Object, required: true}
})

const form = useForm({
    items: props.order.items,
    shipping_amount: 0
})

const onItemsUpdate = (items) => {
    form.items = items;
}

const createReturn = () => {
    form.post(route('admin.orders.returns.store', props.order.id), {})
}
</script>

<template>
    <ActionsPanel back-route="admin.orders.show" :back-route-params="[props.order.id]">
        <template #title>
            <PageTitle>Zwrot zamówienia</PageTitle>
        </template>
    </ActionsPanel>
    <div class="px-6 py-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <Panel width="w-1/2">
                <template #header>
                    Szczegóły zamówienia
                </template>

                <OrderDetails :order="order"/>

            </Panel>

            <Panel width="w-1/2">
                <template #header>
                    Dane zamawiającego
                </template>

                <div class="mb-4 px-4 flex flex-wrap items-start">
                    <div class="w-full sm:w-1/2">
                        <div class="font-semibold text-lg mb-2 flex gap-2">
                            <div>Dane do płatności</div>
                        </div>
                        <div>
                            <div v-if="order.billing_address.first_name || order.billing_address.last_name">
                                {{ order.billing_address.first_name }} {{ order.billing_address.last_name }}
                            </div>
                            <div v-if="order.billing_address.company">{{ order.billing_address.company }}</div>
                            <div v-if="order.billing_address.nip">NIP: {{ order.billing_address.nip }}</div>
                            <div>{{ order.billing_address.postal_code }} {{ order.billing_address.city }}</div>
                            <div>{{ order.billing_address.address_line }}</div>
                            <div>tel.: {{ order.billing_address.phone }}</div>
                        </div>
                    </div>
                </div>
            </Panel>
        </div>

        <Panel>
            <template #header>
                Produkty
            </template>
            <EditReturnItems :items="form.items" @onItemsUpdate="onItemsUpdate"/>
        </Panel>

        <div class="mt-4 flex justify-end">
            <div>
                <FormField label="Zwrot kosztu wysyłki">
                    <Input id="shipping_amount" v-model="form.shipping_amount"/>
                </FormField>
                <Button type="primary" @click="createReturn">Zapisz</Button>
            </div>
        </div>
    </div>
</template>