<script setup>
import BaseModal from "@shopen/components/admin/ui/BaseModal.vue";
import {ref, watch} from "vue";
import AddressForm from "@shopen/components/admin/address/AddressForm.vue";
import { useForm } from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";

const emits = defineEmits(['onClose']);
const props = defineProps({
    user: Object,
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
        form.put(route('admin.users.addresses.update', [props.user.id, props.address.id]), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => { emits('onClose'); }
        });
    } else {
        form.post(route('admin.users.addresses.store', props.user.id), {
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
            <Button type="ghost" full-width @click="emits('onClose')">Anuluj</Button>
            <Button type="primary" full-width @click="save" :loading="form.processing">Zapisz</Button>
        </template>
    </BaseModal>
</template>