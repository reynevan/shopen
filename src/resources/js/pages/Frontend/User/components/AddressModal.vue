<script setup>
import BaseModal from "@shopen/components/frontend/ui/BaseModal.vue";
import {ref, watch} from "vue";
import AddressForm from "@shopen/components/frontend/form/AddressForm.vue";
import { useForm } from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";

const emits = defineEmits(['onClose']);
const props = defineProps({
    show: Boolean,
    address: Object
});

const loading = ref(false);

const initialFormState = {
    id: null,
    first_name: '', last_name: '', company: '', nip: '', email: '',
    address_line: '', postal_code: '', city: '', phone: '', is_default: false,
    type: 'shipping'
};

const form = useForm({...initialFormState});

watch(() => props.address, (newAddress) => {
    form.defaults({...initialFormState});
    form.reset();

    if (newAddress) {
        form.defaults(newAddress);
        form.reset();
    }

    form.clearErrors();
}, { deep: true });

const save = () => {
    if (props.address.id) {
        let routeName = props.address.type === 'shipping' ? 'api.users.shipping-addresses.update' : 'api.users.billing-addresses.update';
        form.put(route(routeName, props.address.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => { emits('onClose'); }
        });
    } else {
        let routeName = props.address.type === 'shipping' ? 'api.users.shipping-addresses.store' : 'api.users.billing-addresses.store';
        form.post(route(routeName), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => { emits('onClose'); }
        });
    }
};

</script>

<template>
    <BaseModal :show="props.show" @onClose="emits('onClose')" :closable-cover="false">
        <template #header>
            <span v-if="address.type === 'shipping'">Adres dostawy</span>
            <span v-else>Dane do płatności</span>
        </template>
        <div class="mb-6">
            <AddressForm :address="form" :errors="form.errors"></AddressForm>
        </div>
        <template #buttons>
            <Button type="cancel" @click="emits('onClose')">Anuluj</Button>
            <Button type="primary" @click="save" :loading="form.processing">Zapisz</Button>
        </template>
    </BaseModal>
</template>