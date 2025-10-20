<script setup>
import Input from "@shopen/components/admin/form/input/Input.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps(['order']);

const form = useForm({
    shipping_tracking_code: '',
})

const submit = () => {
    form.post(route('admin.orders.shipping', props.order.id), {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <div>{{ order.payment_method_label }}</div>
        </div>
        <div class="divide-y space-y-2">
            <div v-for="payment in order.payments" class="flex items-center justify-between py-2">
                <div class="flex flex-col items-start">
                    <div class="text-xs">kwota</div>
                    <div>{{ payment.amount }}</div>
                </div>
                <div class="flex flex-col items-end">
                    <div class="text-xs">status</div>
                    <div>{{ payment.status_label }}</div>
                </div>
            </div>
        </div>
        <div v-if="order.shipping_method_trackable">
            <form @submit.prevent="submit" method="POST">
                <div class="mb-4">
                    <label for="shipping_tracking_code">Numer przesyłki</label>
                    <Input type="text" v-model="form.shipping_tracking_code" id="shipping_tracking_code"/>
                </div>
                <Button role="submit">Zapisz</Button>
            </form>
        </div>
    </div>
</template>