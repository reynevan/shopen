<script setup>
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import {useForm} from "@inertiajs/vue3";
import FormMenu from "@shopen/components/admin/form/menu/FormMenu.vue";
import {ref} from "vue";
import AddressesSection from "./FormSections/AddressesSection.vue";
import OrdersSection from "./FormSections/OrdersSection.vue";
import PageTitle from "../../../../components/admin/ui/PageTitle.vue";


const props = defineProps({
    user: {type: Object, required: true},
    orders: {type: Array},
    defaultShippingAddress: {type: Object},
    defaultBillingAddress: {type: Object},
    shippingAddresses: {type: Array},
    billingAddresses: {type: Array},
    tab: {type: String}
})

const form = useForm({
    first_name: props.user?.first_name,
    last_name: props.user?.last_name,
    email: props.user?.email,
})

const sections = [
    {
        section: 'general',
        title: 'Dane'
    },
    {
        section: 'addresses',
        title: 'Adresy'
    },
    {
        section: 'orders',
        title: 'Zamówienia'
    }
]

const activeSection = ref(props.tab ?? 'general');

const onChangeSection = (section) => {
    activeSection.value = section;
}

const save = () => {

}
</script>

<template>
    <ActionsPanel back-route="admin.users.index">
        <template #title>
            <PageTitle v-if="user && user.id">{{ user.first_name }} {{ user.last_name }}</PageTitle>
            <PageTitle v-else>Nowy użytkownik</PageTitle>
        </template>
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div class="flex items-start gap-6">
        <div class="sticky top-12">
            <FormMenu :sections="sections" @onSelect="onChangeSection" :activeSection="activeSection"/>
        </div>
        <div class="border-l border-light pl-6 w-full">

            <section v-show="activeSection === 'general'">
                <FormField
                    :required="true"
                    :error="form.errors.first_name"
                    label="Imię" label-for="first_name">
                    <Input v-model="form.first_name" id="first_name"/>
                </FormField>

                <FormField
                    :required="true"
                    :error="form.errors.last_name"
                    label="Nazwisko" label-for="last_name">
                    <Input v-model="form.last_name" id="last_name"/>
                </FormField>

                <FormField
                    :required="true"
                    :error="form.errors.email"
                    label="Email" label-for="email">
                    <Input v-model="form.email" type="email" id="email"/>
                </FormField>
            </section>

            <section v-show="activeSection === 'addresses'">
                <AddressesSection
                    :user="user"
                    :defaultShippingAddress="defaultShippingAddress"
                    :defaultBillingAddress="defaultBillingAddress"
                    :shippingAddresses="shippingAddresses"
                    :billingAddresses="billingAddresses"/>
            </section>

            <section v-show="activeSection === 'orders'">
                <OrdersSection :orders="orders"/>
            </section>

        </div>
    </div>

</template>