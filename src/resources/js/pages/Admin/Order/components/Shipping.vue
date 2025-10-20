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
        <div>
            <div>{{ order.shipping_method_label }}</div>
            <div>{{ order.delivery_point_code }}</div>
            <div v-if="order.shipped_at">Wysłano: {{ order.shipped_at }}</div>
            <div v-if="shipping_tracking_code">Numer przesyłki: {{ order.shipping_tracking_code }}</div>
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