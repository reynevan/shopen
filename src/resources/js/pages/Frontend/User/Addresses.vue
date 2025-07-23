<script setup>

import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import Heading from "./components/Heading.vue";
import AddressThumbnail from "./components/AddressThumbnail.vue";
import IconPlus from "../../../components/icons/IconPlus.vue";
import {ref} from "vue";
import AddressModal from "./components/AddressModal.vue";

defineOptions({layout: UserPanelLayout})
defineProps([
    'defaultShippingAddress',
    'defaultBillingAddress',
    'shippingAddresses',
    'billingAddresses',
])

const showAddressModal = ref(false);
const addressToEdit = ref(null);
const editAddress = (_address) => {
    addressToEdit.value = _address;
    showAddressModal.value = true;
};
const addAddress = (type, isDefault) => {
    addressToEdit.value = {
        type,
        is_default: isDefault
    };
    showAddressModal.value = true;
};
</script>

<template>
    <Heading title="Dane do zamówień"/>
    <div>
        <div class="flex flex-col sm:flex-row items-stretch gap-6 mb-6">
            <div class="w-1/2 flex flex-col">
                <h3 class="text-xl mb-2">Domyślny adres dostawy</h3>
                <div class="border rounded w-full h-full">
                    <AddressThumbnail
                        v-if="defaultShippingAddress"
                        @onEdit="editAddress"
                        :selectable="false"
                        :address="defaultShippingAddress"/>
                    <div v-else class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                        <button
                            @click="() => addAddress('shipping', true)"
                            class="button-secondary max-w-[200px] flex items-center justify-center">
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-1/2 flex flex-col">
                <h3 class="text-xl mb-2">Domyślny adres rozliczeniowy</h3>
                <div class="border rounded w-full h-full">
                    <AddressThumbnail
                        v-if="defaultBillingAddress"
                        @onEdit="editAddress"
                        :selectable="false"
                        :address="defaultBillingAddress"/>
                    <div v-else class="flex items-center justify-center w-full h-full sm:w-auto">
                        <button
                            @click="() => addAddress('billing', true)"
                            class="button-secondary max-w-[200px] flex items-center justify-center">
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-6">
            <h3 class="text-xl mb-2">Dodatkowe adresy dostawy</h3>
            <div class="flex border rounded">
                <AddressThumbnail v-for="address in shippingAddresses"
                                  @onEdit="editAddress"
                                  :key="address.id"
                                  :selectable="true"
                                  :address="address"/>
                <div class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                    <button
                        @click="() => addAddress('shipping', false)"
                        class="button-secondary max-w-[200px] flex items-center justify-center">
                        <span>Dodaj adres</span>
                        <IconPlus/>
                    </button>
                </div>
            </div>
        </div>
        <div>
            <h3 class="text-xl mb-2">Dodatkowe adresy rozliczeniowe</h3>
            <div class="flex border rounded">
                <AddressThumbnail v-for="address in billingAddresses"
                                  @onEdit="editAddress"
                                  :key="address.id"
                                  :selectable="true"
                                  :address="address"/>
                <div class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                    <button
                        @click="() => addAddress('billing', false)"
                        class="button-secondary max-w-[200px] flex items-center justify-center">
                        <span>Dodaj adres</span>
                        <IconPlus/>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <AddressModal
        :show="showAddressModal"
        :address="addressToEdit"
        @onClose="showAddressModal = false"
    />

</template>
