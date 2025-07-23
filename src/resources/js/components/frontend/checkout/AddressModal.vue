<script setup>
import BaseModal from "@shopen/components/frontend/ui/BaseModal.vue";
import {ref, watch} from "vue";
import AddressForm from "@shopen/components/frontend/form/AddressForm.vue";
import { useForm } from "@inertiajs/vue3";
import {useAuthStore} from "@shopen/stores/auth.js";
import Button from "@shopen/components/frontend/ui/Button.vue";

const emits = defineEmits(['onClose']);
const auth = useAuthStore();
const props = defineProps({
    show: Boolean,
    address: Object
});

const loading = ref(false);

const form = useForm({
    id: null,
    first_name: '', last_name: '', company: '', nip: '', email: '',
    address_line: '', postal_code: '', city: '', phone: '', is_default: false
});

watch(() => props.address, (newAddress) => {
    if (newAddress) {
        form.defaults(newAddress).reset();
    } else {
        form.reset();
    }
    form.clearErrors();
}, { deep: true });

const save = () => {
    if (auth.isLoggedIn) {
        if (props.address.id) {
            let routeName = props.address.type === 'shipping' ? 'api.users.shipping-addresses.update' : 'api.users.billing-addresses.update';
            form.put(route(routeName, props.address.id), {
                preserveState: true,
                preserveScroll: true,
                only: ['addresses', 'selectedShippingAddress', 'selectedBillingAddress'],
                onSuccess: () => { emits('onClose'); }
            });
        } else {
            let routeName = props.address.type === 'shipping' ? 'api.users.shipping-addresses.store' : 'api.users.billing-addresses.store';
            form.post(route(routeName), {
                preserveState: true,
                preserveScroll: true,
                only: ['addresses', 'selectedShippingAddress', 'selectedBillingAddress'],
                onSuccess: () => { emits('onClose'); }
            });
        }
    } else {
        let routeName = props.address.type === 'shipping' ? 'checkout.update-shipping-address' : 'checkout.update-billing-address';
        form.put(route(routeName), {
            preserveState: true,
            preserveScroll: true,
            only: ['addresses', 'selectedShippingAddress', 'selectedBillingAddress'],
            onSuccess: () => { emits('onClose'); }
        });
    }
};

</script>

<template>
    <BaseModal :show="props.show">
        <div class="mb-6">
            <AddressForm :address="form" :errors="form.errors"></AddressForm>
        </div>
        <div class="flex items-center gap-2">
            <Button type="cancel" @click="emits('onClose')">Anuluj</Button>
            <Button type="primary" @click="save" :loading="form.processing">Zapisz</Button>
        </div>
    </BaseModal>
</template>