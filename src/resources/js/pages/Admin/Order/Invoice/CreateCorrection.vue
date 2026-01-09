<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "../../../../components/admin/ui/ActionsPanel.vue";
import PageTitle from "../../../../components/admin/ui/PageTitle.vue";
import Panel from "../../../../components/admin/ui/Panel.vue";
import EditInvoiceItems from "../components/Invoice/EditInvoiceItems.vue";
import {useForm} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";
import {ref} from "vue";
import Input from "../../../../components/admin/form/input/Input.vue";
import DateInput from "../../../../components/admin/form/input/DateInput.vue";
import Button from "../../../../components/admin/ui/Button.vue";
import FormField from "../../../../components/admin/form/FormField.vue";
import Toggle from "../../../../components/admin/form/input/Toggle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    invoice: {type: Object, required: true},
    number: {type: String}
})

const form = useForm({
    billing_address: props.invoice.billing_address,
    items: props.invoice.items,
    number: props.number,
    send_email: false,
    correction_reason: '',
    payment_due_date: props.invoice.payment_due_date,
    payment_method: props.invoice.payment_method_label,
})

const isAnyItemEditing = ref(false)

const onEditingChange = (editing) => {
    isAnyItemEditing.value = editing;
}

const onItemsUpdate = (items) => {
    form.items = items;
}

const editingAddress = ref(false)
const editAddress = () => {
    editingAddress.value = !editingAddress.value;
}

const createInvoice = () => {
    form.post(route('admin.orders.invoices.corrections.store', props.invoice.id), {})
}
</script>

<template>
    <ActionsPanel back-route="admin.orders.invoices.show" :back-route-params="[props.invoice.id]">
        <template #title>
            <PageTitle>Nowa faktura korygująca</PageTitle>
        </template>
    </ActionsPanel>
    <div class="px-6 py-8">
        <div class="flex gap-4 flex-col sm:flex-row">

            <Panel width="w-1/2">
                <template #header>
                    Szczegóły
                </template>

                <div class="sm:max-w-[400px] w-full">
                    <FormField label="Numer faktury" :error="form.errors.number">
                        <Input id="number" v-model="form.number"/>
                    </FormField>
                    <FormField label="Przyczyna korekty" :error="form.errors.correction_reason">
                        <Input id="correction_reason" v-model="form.correction_reason"/>
                    </FormField>
                    <FormField label="Termin płatności" :error="form.errors.payment_due_date">
                        <DateInput id="payment_due_date" v-model="form.payment_due_date"/>
                    </FormField>
                    <FormField label="Metoda płatności" :error="form.errors.payment_method">
                        <Input id="payment_method" v-model="form.payment_method"/>
                    </FormField>
                </div>
            </Panel>

            <Panel width="w-1/2">
                <template #header>
                    Dane zamawiającego
                </template>

                <div class="mb-4 px-4 flex flex-wrap items-start">
                    <div class="w-full sm:w-1/2">
                        <div class="font-semibold text-lg mb-2 flex gap-2">
                            <div>Dane do płatności</div>
                            <ActionButton type="edit" @click="editAddress">Edytuj</ActionButton>
                        </div>
                        <div v-if="editingAddress">
                            <div class="flex gap-2 items-center mb-2">
                                <FormField label="Imię" field="first_name">
                                    <Input id="first_name" v-model="form.billing_address.first_name"
                                           placeholder="Imię"/>
                                </FormField>
                                <FormField label="Nazwisko" field="last_name">
                                    <Input id="last_name" v-model="form.billing_address.last_name"
                                           placeholder="Nazwisko"/>
                                </FormField>
                            </div>
                            <div class="flex gap-2 items-center mb-2">
                                <FormField label="Nazwa firmy" field="company">
                                    <Input id="company" v-model="form.billing_address.company"
                                           placeholder="Nazwa firmy"/>
                                </FormField>
                                <FormField label="NIP" field="nip">
                                    <Input id="nip" v-model="form.billing_address.nip" placeholder="NIP"/>
                                </FormField>
                            </div>
                            <div class="flex gap-2 items-center mb-2">
                                <FormField label="NIP" field="nip">
                                    <Input id="postal_code" v-model="form.billing_address.postal_code"
                                           placeholder="Kod pocztowy"/>
                                </FormField>
                                <FormField label="NIP" field="nip">
                                    <Input id="city" v-model="form.billing_address.city" placeholder="Miasto"/>
                                </FormField>
                            </div>
                            <div class="mb-2">
                                <FormField label="Adres" field="address_line">
                                    <Input id="address_line" v-model="form.billing_address.address_line"
                                           placeholder="Adres"/>
                                </FormField>
                            </div>
                            <div>
                                <FormField label="Nr telefonu" field="phone">
                                    <Input id="phone" v-model="form.billing_address.phone" placeholder="Nr telefonu"/>
                                </FormField>
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="form.billing_address.first_name || form.billing_address.last_name">
                                {{ form.billing_address.first_name }} {{ form.billing_address.last_name }}
                            </div>
                            <div v-if="form.billing_address.company">{{ form.billing_address.company }}</div>
                            <div v-if="form.billing_address.nip">NIP: {{ form.billing_address.nip }}</div>
                            <div>{{ form.billing_address.postal_code }} {{ form.billing_address.city }}</div>
                            <div>{{ form.billing_address.address_line }}</div>
                            <div>tel.: {{ form.billing_address.phone }}</div>
                        </div>
                    </div>
                </div>
            </Panel>
        </div>

        <Panel>
            <template #header>
                Produkty
            </template>
            <EditInvoiceItems
                :items="form.items"
                @onEditingChange="onEditingChange"
                @onItemsUpdate="onItemsUpdate"/>
        </Panel>

        <div class="mt-4 flex justify-end">
            <div>
                <FormField label="Wyślij e-mail do klienta">
                    <Toggle id="send_email" v-model="form.send_email"/>
                </FormField>
                <Button type="primary" @click="createInvoice" :disabled="isAnyItemEditing">Zapisz</Button>
            </div>
        </div>
    </div>
</template>