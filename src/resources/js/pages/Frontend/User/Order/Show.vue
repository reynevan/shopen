<script setup>
import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import OrderItems from "@shopen/pages/Frontend/User/Order/components/OrderItems.vue";
import OrderAmounts from "@shopen/pages/Frontend/User/Order/components/OrderAmounts.vue";
import { router} from "@inertiajs/vue3";
import {useConfirm} from "../../../../composables/useConfirm";
import Button from "../../../../components/frontend/ui/Button.vue";


defineOptions({layout: UserPanelLayout})
const props = defineProps(['order'])

const { confirm } = useConfirm();

const cancelOrder = async () => {
    const isConfirmed = await confirm({
        title: 'Potwierdź anulowanie',
        message: 'Czy na pewno chcesz anulować to zamówienie?',
        confirmButtonText: 'Tak, anuluj',
        cancelButtonText: 'Nie, wróć'
    });

    if (!isConfirmed) {
        return;
    }

    router.put(route('user.orders.cancel', props.order.uuid))
}

const pay = () => {
    router.post(route('order.pay', props.order.uuid))
}
</script>

<template>
    <div class="order-show py-10">
        <!-- Status zamówienia - wyeksponowany jako najważniejsza informacja -->
        <div class="order-header">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <div class="flex items-center flex-wrap sm:flex-nowrap gap-2">
                        <div class="order-number">
                            Zamówienie #{{ order.order_number }}
                        </div>
                        <div class="order-status">
                            {{ order.status_label }}
                        </div>
                    </div>
                    <div class="order-date">złożone {{ order.placed_time }}</div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <a v-if="order.can_cancel" @click.prevent="cancelOrder" class="text-sm text-red-400 hover:text-red-600 transition-all cursor-pointer">Anuluj zamówienie</a>
                </div>
            </div>
        </div>

        <!-- Informacje o wysyłce i płatności -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Dane wysyłkowe -->
            <div class="order-address">
                <h3 class="address-label">Adres dostawy</h3>
                <div class="address">
                    <div class="font-medium">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</div>
                    <div class="address-line" v-if="order.shipping_address.company">{{ order.shipping_address.company }}</div>
                    <div class="address-line">{{ order.shipping_address.address_line }}</div>
                    <div class="address-line">{{ order.shipping_address.postal_code }} {{ order.shipping_address.city }}</div>
                    <div class="address-line" v-if="order.shipping_address.country">{{ order.shipping_address.country }}</div>
                    <div class="address-line">{{ order.shipping_address.phone }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="method-label">Metoda dostawy</div>
                    <div class="font-medium">{{ order.shipping_method_label }}</div>
                    <div v-if="order.delivery_point_code" class="mt-1 text-sm">
                        Punkt odbioru: {{ order.delivery_point_code }}
                    </div>
                    <div v-if="order.shipping_tracking_code" class="mt-2">
                        <div class="text-sm text-neutral-500">Numer przesyłki</div>
                        <div class="font-medium">{{ order.shipping_tracking_code }}</div>
                    </div>
                </div>
            </div>

            <!-- Dane do faktury -->
            <div class="order-address">
                <h3 class="address-label">Dane do faktury</h3>
                <div class="address">
                    <div class="font-medium">{{ order.billing_address.first_name }} {{ order.billing_address.last_name }}</div>
                    <div class="address-line" v-if="order.billing_address.company">{{ order.billing_address.company }}</div>
                    <div class="address-line">{{ order.billing_address.address_line }}</div>
                    <div class="address-line">{{ order.billing_address.postal_code }} {{ order.billing_address.city }}</div>
                    <div class="address-line" v-if="order.billing_address.country">{{ order.billing_address.country }}</div>
                    <div class="address-line">{{ order.billing_address.phone }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="method-label">Metoda płatności</div>
                    <div class="font-medium">{{ order.payment_method_label }}</div>
                    <Button v-if="order.can_pay" @click="pay">Zapłać</Button>
                </div>
            </div>
        </div>

        <!-- Zamówione produkty -->
        <div class="order-items">
            <h3 class="items-label">Zamówione produkty</h3>
            <OrderItems :items="order.items"/>
        </div>

        <!-- Podsumowanie kwot -->
        <div class="order-summary">
            <OrderAmounts :order="order"/>
        </div>
    </div>
</template>