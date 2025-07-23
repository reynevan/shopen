<script setup>
import {onMounted, ref} from "vue";
import { useAuthStore } from "@shopen/stores/auth.js";
import AddressModal from "@shopen/components/frontend/checkout/AddressModal.vue";
import AddressThumbnail from "@shopen/components/frontend/checkout/AddressThumbnail.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";
import { router } from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import {useCheckoutStore} from "@shopen/stores/checkout.js";

const auth = useAuthStore();
const props = defineProps(['maxAddresses', 'addresses']);
const checkout = useCheckoutStore();

const addressToEdit = ref(null);
const showAddressModal = ref(false);

const selectAddress = (address) => {
    if (address.id) {
        router.put(route('checkout.select-billing-address'), {
            'id': address.id,
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['addresses']
        });
    }
};

const editAddress = (_address) => {
    addressToEdit.value = _address;
    showAddressModal.value = true;
};

const openNewAddressModal = () => {
    addressToEdit.value = {
        type: 'billing'
    };
    showAddressModal.value = true;
};

</script>

<template>
    <div>

        <div>
            <div class="checkout-section-title">Dane do płatności</div>
            <div>
                <FormField>
                    <label for="billing-same-as-shipping">
                        <input type="checkbox" id="billing-same-as-shipping" v-model="checkout.customBillingAddress">
                        <span class="text-neutral-500 ml-2">Adres rozliczeniowy inny niż adres dostawy</span>
                    </label>
                </FormField>
                <div v-if="checkout.customBillingAddress">
                    <div
                        class="border min-h-[100px] flex flex-wrap items-stretch gap-2"
                        :class="{'items-center justify-center': !addresses.length}">

                        <AddressThumbnail v-for="address in addresses"
                                          :key="address.id"
                                          :address="address"
                                          @onEdit="editAddress"
                        />
                        <div v-if="(auth.isLoggedIn && addresses.length < maxAddresses) || (!auth.isLoggedIn && !addresses.length)"
                             class="flex items-center justify-center w-full sm:w-auto px-6 my-6">
                            <button @click="openNewAddressModal"
                                    class="button-secondary max-w-[200px] flex items-center justify-center">
                                <span>Dodaj adres</span>
                                <IconPlus/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <AddressModal
            :show="showAddressModal"
            :address="addressToEdit"
            @onClose="showAddressModal = false"
        />

    </div>
</template>

<style scoped>

</style>