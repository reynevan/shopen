<script setup>

import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import Heading from "@shopen/pages/Frontend/User/components/Heading.vue";
import AddressThumbnail from "@shopen/pages/Frontend/User/components/AddressThumbnail.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";
import {ref} from "vue";
import AddressModal from "@shopen/pages/Frontend/User/components/AddressModal.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {Head} from "@inertiajs/vue3";

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
    <Head title="Dane do zamówień"/>
    <Heading title="Dane do zamówień"/>
    <main>
        <div class="flex flex-col sm:flex-row items-stretch gap-6 mb-6">

            <div class="w-full sm:w-1/2 flex flex-col">
                <h3 class="text-xl mb-2">Domyślny adres dostawy</h3>
                <div class="border rounded w-full h-full">
                    <AddressThumbnail
                        v-if="defaultShippingAddress"
                        @onEdit="editAddress"
                        :selectable="false"
                        :address="defaultShippingAddress"/>
                    <div v-else class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                        <Button type="secondary"
                                @click="() => addAddress('shipping', true)"
                                class="max-w-[200px]">
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </Button>
                    </div>
                </div>
            </div>
            <div class="w-full sm:w-1/2 flex flex-col">
                <h3 class="text-xl mb-2">Domyślny adres rozliczeniowy</h3>
                <div class="border rounded w-full h-full">
                    <AddressThumbnail
                        v-if="defaultBillingAddress"
                        @onEdit="editAddress"
                        :selectable="false"
                        :address="defaultBillingAddress"/>
                    <div v-else class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                        <Button type="secondary"
                                @click="() => addAddress('billing', true)"
                                class="max-w-[200px]">
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-6">
            <h3 class="text-xl mb-2">Dodatkowe adresy dostawy</h3>
            <div class="flex flex-col sm:flex-row items-center sm:items-start border rounded">
                <div class="border-b border-light sm:border-0"
                     v-for="address in shippingAddresses"
                     :key="address.id">
                    <AddressThumbnail @onEdit="editAddress"
                                      :selectable="true"
                                      :address="address"/>
                </div>
                <div class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                    <Button type="secondary"
                            @click="() => addAddress('shipping', false)">
                        <span>Dodaj adres</span>
                        <IconPlus/>
                    </Button>
                </div>
            </div>
        </div>
        <div>
            <h3 class="text-xl mb-2">Dodatkowe adresy rozliczeniowe</h3>
            <div class="flex flex-col sm:flex-row items-center sm:items-start border rounded">
                <div class="border-b border-light sm:border-0"
                     v-for="address in billingAddresses"
                     :key="address.id">
                <AddressThumbnail @onEdit="editAddress"
                                  :selectable="true"
                                  :address="address"/>
                </div>
                <div class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                    <Button type="secondary"
                            @click="() => addAddress('billing', false)">
                        <span>Dodaj adres</span>
                        <IconPlus/>
                    </Button>
                </div>
            </div>
        </div>
    </main>


    <AddressModal
        :show="showAddressModal"
        :address="addressToEdit"
        @onClose="showAddressModal = false"
    />

</template>
