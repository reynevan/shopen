<script setup>
import {router, useForm} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";
import {computed} from "vue";

const props = defineProps(['order']);

const form = useForm({
    shipping_tracking_code: '',
})

const sendMail = () => {
    router.post(route('admin.orders.send-vouchers', props.order.id), {
        preserveScroll: true
    })
}

const allSent = computed(() => !props.order.items.some(item => !item.promo_code_coupon_email_sent))
</script>

<template>
    <div class="divide-y divide-border-light">
        <template v-for="item in order.items" :key="item.id">
            <div v-if="item.promo_code_coupons" class="flex items-center gap-2 py-2">
                <div>{{ item.product.promo_code.name }}</div>
                <div v-for="coupon in item.promo_code_coupons" class="border text-sm px-2 bg-white">
                    {{ coupon.code }}
                </div>
                <div v-if="item.promo_code_coupon_email_sent" class="text-gray-500 text-lg">
                    <i class="bi bi-send-check" title="Kod wysłany do klienta"></i>
                </div>
            </div>
        </template>

        <div class="mt-4">
            <ActionButton type="mail" @click="sendMail">
                <span v-if="allSent">Wyślij email ponownie</span>
                <span v-else>Wyślij email</span>
            </ActionButton>
        </div>
    </div>
</template>