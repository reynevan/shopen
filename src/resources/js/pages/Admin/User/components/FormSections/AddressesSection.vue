<script setup>
import AddressThumbnail from "@shopen/components/admin/address/AddressThumbnail.vue";
import {ref} from "vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import AddressModal from "@shopen/pages/Admin/User/components/AddressModal.vue";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    defaultShippingAddress: {type: Object },
    defaultBillingAddress: {type: Object },
    shippingAddresses: {type: Array },
    billingAddresses: {type: Array },
    user: {type: Object },
})
const showAddressModal = ref(false)
const addressToEdit = ref(null);

const editAddress = (_address) => {
    addressToEdit.value = {..._address};
    showAddressModal.value = true;
};

const addAddress = (type, isDefault) => {
    addressToEdit.value = {
        type,
        is_default: isDefault,
    };
    showAddressModal.value = true;
};

const removeAddress = (_address) => {
    if (!confirm('Na pewno chcesz usunąć ten adres?')) {
        return;
    }
    router.delete(route('admin.users.addresses.delete', [props.user.id, _address.id]));
}

const closeAddressModal = () => {
    showAddressModal.value = false;
    addressToEdit.value = null;
}
</script>

<template>
    <div class="flex gap-6">
        <div class="w-1/2">
            <div class="flex justify-between border-b pb-2">
                <p class="mb-4 text-lg font-semibold">Adresy dostawy</p>
                <ActionButton @click="addAddress('shipping')" type="add">Dodaj adres</ActionButton>
            </div>
            <div class="my-6 border rounded p-4 min-h-20">
                <p class="text-xs uppercase mb-2">ADRES DOMYŚLNY</p>
                <div v-if="defaultShippingAddress">
                    <AddressThumbnail size="lg" :address="defaultShippingAddress"/>
                    <ActionButtons>
                        <ActionButton type="edit" @click="editAddress(defaultShippingAddress)">Edytuj</ActionButton>
                        <ActionButton type="remove" @click="removeAddress(defaultShippingAddress)">Usuń</ActionButton>
                    </ActionButtons>
                </div>
                <p v-else class="text-gray-400 text-sm py-6">Brak domyślnego adresu</p>
            </div>
            <div v-if="shippingAddresses?.length > 0">
                <p>Dodatkowe adresy dostawy</p>
                <div class="mt-2 flex flex-wrap gap-4">
                    <div v-for="address in shippingAddresses" class="p-4 border rounded flex flex-col justify-between">
                        <div class="mb-4">
                            <AddressThumbnail :address="address" />
                        </div>
                        <ActionButtons>
                            <ActionButton type="edit" @click="editAddress(address)">Edytuj</ActionButton>
                            <ActionButton type="remove" @click="removeAddress(address)">Usuń</ActionButton>
                        </ActionButtons>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-1/2">
            <div class="flex justify-between border-b pb-2">
                <p class="mb-4 text-lg font-semibold">Adresy rozliczeniowe</p>
                <ActionButton @click="addAddress('billing')" type="add">Dodaj adres</ActionButton>
            </div>
            <div class="my-6 border rounded p-4 min-h-20">
                <p class="text-xs uppercase mb-2">ADRES DOMYŚLNY</p>
                <div v-if="defaultBillingAddress">
                <AddressThumbnail size="lg" :address="defaultBillingAddress"/>
                    <ActionButtons>
                        <ActionButton type="edit" @click="editAddress(defaultBillingAddress)">Edytuj</ActionButton>
                        <ActionButton type="remove" @click="removeAddress(defaultBillingAddress)">Usuń</ActionButton>
                    </ActionButtons>
                </div>
                <p v-else class="text-gray-400 text-sm py-6">Brak domyślnego adresu</p>
            </div>
            <div v-if="billingAddresses?.length > 0">
                <p>Dodatkowe adresy rozliczeniowe</p>
                <div class="mt-2 flex flex-wrap gap-4">
                    <div v-for="address in billingAddresses" class="p-4 border rounded flex flex-col justify-between">
                        <div class="mb-4">
                            <AddressThumbnail :address="address" />
                        </div>
                        <ActionButtons>
                            <ActionButton type="edit" @click="editAddress(address)">Edytuj</ActionButton>
                            <ActionButton type="remove" @click="removeAddress(address)">Usuń</ActionButton>
                        </ActionButtons>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <AddressModal :show="showAddressModal"
                  :user="user"
                  :closable="false"
                  :address="addressToEdit"
                  @onClose="closeAddressModal"
    />
</template>