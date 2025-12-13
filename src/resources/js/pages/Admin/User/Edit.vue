<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import {Head} from "@inertiajs/vue3";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import UserForm from "./components/UserForm.vue";

defineOptions({layout: AdminLayout})

defineProps({
    user: {type: Object},
    orders: {type: Array},
    defaultShippingAddress: {type: Object },
    defaultBillingAddress: {type: Object },
    shippingAddresses: {type: Array },
    billingAddresses: {type: Array },
    tab: {type: String}
});
</script>
<template>
    <Head title="Klient - edycja"/>
    <main>
        <UserForm
            :user="user"
            :orders="orders"
            :defaultShippingAddress="defaultShippingAddress"
            :defaultBillingAddress="defaultBillingAddress"
            :shippingAddresses="shippingAddresses"
            :billingAddresses="billingAddresses"
            :tab="tab"
        />
    </main>
</template>