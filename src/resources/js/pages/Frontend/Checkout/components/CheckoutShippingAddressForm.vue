<script setup>
import { useShippingStore } from "@shopen/stores/shipping.js";
import {ref} from "vue";
import { useAuthStore } from "@shopen/stores/auth.js";
import AddressModal from "@shopen/pages/Frontend/Checkout/components//AddressModal.vue";
import AddressThumbnail from "@shopen/pages/Frontend/Checkout/components//AddressThumbnail.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";
import { router } from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";

const shipping = useShippingStore();
const auth = useAuthStore();
const props = defineProps(['maxAddresses', 'addresses']);

const addressToEdit = ref(null);
const showAddressModal = ref(false);



const editAddress = (_address) => {
    addressToEdit.value = _address;
    showAddressModal.value = true;
};

const openNewAddressModal = () => {
    addressToEdit.value = {
        type: 'shipping'
    };
    showAddressModal.value = true;
};
</script>

<template>
    <div>
        <div class="checkout-section-title flex justify-between">
            <span>Adres dostawy</span>
            <Button type="secondary" @click="openNewAddressModal" size="sm"
                    v-if="(auth.isLoggedIn && addresses.length && addresses.length < maxAddresses)">
                <span>Dodaj adres</span>
                <IconPlus/>
            </Button>
        </div>
        <div class="min-h-[100px] flex flex-wrap items-stretch gap-4" :class="{'items-center justify-center': !addresses.length}">
            <AddressThumbnail v-for="address in addresses"
                              :key="address.id"
                              :address="address"
                              :selectable="auth.isLoggedIn"
                              @onEdit="editAddress"/>
            <div v-if="!addresses.length"
                 class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                <Button type="secondary" @click="openNewAddressModal">
                    <span>Dodaj adres</span>
                    <IconPlus/>
                </Button>
            </div>
        </div>

        <AddressModal
            :show="showAddressModal"
            :address="addressToEdit"
            @onClose="showAddressModal = false"
        />
    </div>
</template>