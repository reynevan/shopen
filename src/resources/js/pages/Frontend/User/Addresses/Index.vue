<script setup>
import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import Heading from "@shopen/pages/Frontend/User/components/Heading.vue";
import AddressThumbnail from "@shopen/pages/Frontend/User/components/AddressThumbnail.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";
import {ref} from "vue";
import AddressModal from "@shopen/pages/Frontend/User/components/AddressModal.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {Head} from "@inertiajs/vue3";
import IconHomeAdd from "../../../../components/icons/IconHomeAdd.vue";

defineOptions({layout: UserPanelLayout});

defineProps([
    "defaultShippingAddress",
    "defaultBillingAddress",
    "shippingAddresses",
    "billingAddresses",
]);

const showAddressModal = ref(false);
const addressToEdit = ref(null);
const activeTab = ref("shipping"); // 'shipping' | 'billing'

const editAddress = (_address) => {
    addressToEdit.value = _address;
    showAddressModal.value = true;
};

const addAddress = (type, isDefault) => {
    addressToEdit.value = {
        type,
        is_default: isDefault,
    };
    showAddressModal.value = true;
};
</script>

<template>
    <Head title="Dane do zamówień"/>
    <Heading title="Dane do zamówień"/>

    <!-- Tabs -->
    <div class="mb-6">
        <nav
            role="tablist"
            aria-label="Zakładki adresów"
            class="flex items-stretch gap-2 border-b"
        >
            <button
                role="tab"
                :aria-selected="activeTab === 'shipping'"
                aria-controls="tab-shipping"
                class="cursor-pointer w-1/2 sm:w-auto px-4 py-2 text-sm font-medium -mb-px rounded-t-md border transition-colors"
                :class="activeTab === 'shipping'
                    ? 'border-gray-300 border-b-white text-gray-900 bg-white'
                    : 'border-transparent text-gray-600 hover:text-gray-900'"
                @click="activeTab = 'shipping'"
            >
                Adresy dostawy
            </button>

            <button
                role="tab"
                :aria-selected="activeTab === 'billing'"
                aria-controls="tab-billing"
                class="cursor-pointer w-1/2 sm:w-auto px-4 py-2 text-sm font-medium -mb-px rounded-t-md border transition-colors"
                :class="activeTab === 'billing'
                    ? 'border-gray-300 border-b-white text-gray-900 bg-white'
                    : 'border-transparent text-gray-600 hover:text-gray-900'"
                @click="activeTab = 'billing'"
            >
                Adresy rozliczeniowe
            </button>
        </nav>
    </div>

    <div>
        <!-- SHIPPING TAB -->
        <section
            id="tab-shipping"
            role="tabpanel"
            :aria-hidden="activeTab !== 'shipping'"
            v-show="activeTab === 'shipping'"
        >
            <div class="flex flex-col sm:flex-row items-stretch gap-6 mb-6">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <h3 class="text-xl mb-2">Domyślny adres dostawy</h3>
                    <div class="border rounded w-full h-full" v-if="defaultShippingAddress">
                        <AddressThumbnail
                            @onEdit="editAddress"
                            :selectable="false"
                            :address="defaultShippingAddress"
                        />
                    </div>
                    <div
                        v-else
                        class="border rounded flex items-center justify-center h-full w-full sm:w-auto px-6 py-4"
                    >
                        <Button
                            type="secondary"
                            @click="() => addAddress('shipping', true)"
                            class="max-w-[200px]"
                        >
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </Button>
                    </div>
                </div>

                <div class="w-full sm:w-1/2 flex flex-col">
                    <div class="flex justify-between items-start">
                        <h3 class="text-xl mb-2">Dodatkowe adresy dostawy</h3>
                        <Button type="secondary" size="sm" @click="() => addAddress('shipping', false)">
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </Button>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-start border rounded">
                        <div
                            class="border-b border-light sm:border-0"
                            v-for="address in shippingAddresses"
                            :key="address.id"
                        >
                            <AddressThumbnail
                                @onEdit="editAddress"
                                :selectable="true"
                                :address="address"
                            />
                        </div>
                        <div v-if="!shippingAddresses || !shippingAddresses.length"
                             class="flex items-center justify-center w-full sm:w-auto px-8 my-6 text-neutral-400 gap-2">
                            Nie masz jeszcze żadnych dodatkowych adresów
                            <IconHomeAdd size="4xl"/>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BILLING TAB -->
        <section
            id="tab-billing"
            role="tabpanel"
            :aria-hidden="activeTab !== 'billing'"
            v-show="activeTab === 'billing'"
        >
            <div class="flex flex-col sm:flex-row items-stretch gap-6 mb-6">
                <div class="w-full sm:w-1/2 flex flex-col">
                    <h3 class="text-xl mb-2">Domyślny adres rozliczeniowy</h3>
                    <div class="border rounded w-full h-full" v-if="defaultBillingAddress">
                        <AddressThumbnail
                            @onEdit="editAddress"
                            :selectable="false"
                            :address="defaultBillingAddress"
                        />
                    </div>
                    <div
                        v-else
                        class="border rounded flex items-center justify-center h-full w-full sm:w-auto px-6 py-4"
                    >
                        <Button
                            type="secondary"
                            @click="() => addAddress('billing', true)"
                            class="max-w-[200px]"
                        >
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </Button>
                    </div>
                </div>

                <div class="w-full sm:w-1/2 flex flex-col">
                    <div class="flex justify-between items-start">
                        <h3 class="text-xl mb-2">Dodatkowe adresy rozliczeniowe</h3>
                        <Button type="secondary" size="sm" @click="() => addAddress('billing', false)">
                            <span>Dodaj adres</span>
                            <IconPlus/>
                        </Button>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-start border rounded">
                        <div
                            class="border-b border-light sm:border-0"
                            v-for="address in billingAddresses"
                            :key="address.id"
                        >
                            <AddressThumbnail
                                @onEdit="editAddress"
                                :selectable="true"
                                :address="address"
                            />
                        </div>
                        <div v-if="!billingAddresses || !billingAddresses.length"
                             class="flex items-center justify-center w-full sm:w-auto px-8 my-6 text-neutral-400 gap-2">
                            Nie masz jeszcze żadnych dodatkowych adresów
                            <IconHomeAdd size="4xl"/>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <AddressModal
        :show="showAddressModal"
        :address="addressToEdit"
        @onClose="showAddressModal = false"
    />
</template>