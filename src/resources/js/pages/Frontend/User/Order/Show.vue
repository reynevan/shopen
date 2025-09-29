<script setup>
import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import OrderItems from "../../../../components/frontend/order/OrderItems.vue";
import OrderAmounts from "../../../../components/frontend/order/OrderAmounts.vue";
import {Link, router} from "@inertiajs/vue3";
import {useConfirm} from "../../../../composables/useConfirm";


defineOptions({layout: UserPanelLayout})
const props = defineProps(['order'])

const { confirm } = useConfirm();

const cancelOrder = async () => {
    const isConfirmed = await confirm({
        title: 'Potwierdź anulowanie',
        message: 'Czy na pewno chcesz anulować to zamówienie?',
        confirmButtonText: 'Tak, anuluj',
        cancelButtonText: 'Nie, wróć',
        confirmButtonType: 'danger'
    });

    if (!isConfirmed) {
        return;
    }

    router.put(route('user.orders.cancel', props.order.uuid))
}
</script>

<template>
    <main class="py-10">
        <!-- Status zamówienia - wyeksponowany jako najważniejsza informacja -->
        <div class="mb-6 rounded-r-lg p-4 border-l-4" :class="{
              'border-blue-500 bg-blue-50': order.status === 'new',
              'border-yellow-500 bg-yellow-50': order.status === 'processing' || order.status === 'paid',
              'border-green-500 bg-green-50': order.status === 'shipped' || order.status === 'completed',
              'border-red-500 bg-red-50': order.status === 'canceled' || order.status === 'refunded'
            }">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <div class="text-sm text-gray-500 mb-1">Status zamówienia</div>
                    <div class="text-2xl font-bold" :class="{
                        'text-blue-700': order.status === 'new',
                        'text-yellow-700': order.status === 'processing' || order.status === 'paid',
                        'text-green-700': order.status === 'shipped' || order.status === 'completed',
                        'text-red-700': order.status === 'canceled' || order.status === 'refunded'
                      }">
                        {{ order.status_label }}
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <div class="text-xl">
                        Zamówienie <span class="font-semibold">{{ order.order_number }}</span>
                    </div>
                    <div class="text-sm text-neutral-600">złożone {{ order.placed_time }}</div>
                    <a v-if="order.can_cancel" @click.prevent="cancelOrder" class="text-sm text-red-400 hover:text-red-600 transition-all cursor-pointer">Anuluj zamówienie</a>
                </div>
            </div>
        </div>

        <!-- Informacje o wysyłce i płatności -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Dane wysyłkowe -->
            <div class="bg-white rounded-lg p-5 shadow-sm">
                <h3 class="text-lg font-semibold mb-3">Adres dostawy</h3>
                <div class="text-neutral-800">
                    <div class="font-medium">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</div>
                    <div v-if="order.shipping_address.company">{{ order.shipping_address.company }}</div>
                    <div>{{ order.shipping_address.address_line }}</div>
                    <div>{{ order.shipping_address.postal_code }} {{ order.shipping_address.city }}</div>
                    <div>{{ order.shipping_address.country }}</div>
                    <div class="mt-2">{{ order.shipping_address.phone }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="text-sm text-neutral-500">Metoda dostawy</div>
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
            <div class="bg-white rounded-lg p-5 shadow-sm">
                <h3 class="text-lg font-semibold mb-3">Dane do faktury</h3>
                <div class="text-neutral-800">
                    <div class="font-medium">{{ order.billing_address.first_name }} {{ order.billing_address.last_name }}</div>
                    <div v-if="order.billing_address.company">{{ order.billing_address.company }}</div>
                    <div>{{ order.billing_address.address_line }}</div>
                    <div>{{ order.billing_address.postal_code }} {{ order.billing_address.city }}</div>
                    <div>{{ order.billing_address.country }}</div>
                    <div class="mt-2">{{ order.billing_address.phone }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="text-sm text-neutral-500">Metoda płatności</div>
                    <div class="font-medium">{{ order.payment_method_label }}</div>
                </div>
            </div>
        </div>

        <!-- Zamówione produkty -->
        <div class="bg-white rounded-lg p-5 shadow-sm mb-6">
            <h3 class="text-lg font-semibold mb-4">Zamówione produkty</h3>
            <OrderItems :items="order.items"/>
        </div>

        <!-- Podsumowanie kwot -->
        <div class="bg-white rounded-lg p-5 shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Podsumowanie</h3>
            <OrderAmounts :order="order"/>
        </div>
    </main>
</template>