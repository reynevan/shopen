<script setup>
import Input from "@shopen/components/admin/form/input/Input.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import {useForm} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";
import {ref} from "vue";

const props = defineProps(['order']);

const form = useForm({
    shipping_tracking_code: '',
})

const editing = ref(false)

const submit = () => {
    form.post(route('admin.orders.shipping', props.order.id), {
        preserveState: true,
        preserveScroll: true
    })
    form.reset()
    editing.value = false
}

</script>

<template>
    <div>
        <div>
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="text-xs">metoda wysyłki</div>
                    <div>{{ order.shipping_method_label }}</div>
                </div>
                <div v-if="order.delivery_point_code">
                    <div class="text-xs">punkt odbioru</div>
                    <div>{{ order.delivery_point_code }}</div>
                </div>
            </div>
            <div class="flex justify-between items-center mb-4" v-if="order.shipped_at || order.shipping_tracking_code">
                <div v-if="order.shipped_at">
                    <div class="text-xs">data wysłania</div>
                    <div>{{ order.shipped_at }}</div>
                </div>
                <div v-if="order.shipping_tracking_code">
                    <div class="text-xs">numer przesyłki</div>
                    <div class="flex gap-2 items-center">
                        {{ order.shipping_tracking_code }}
                        <ActionButton type="edit" @click="editing = true"/>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="order.shipping_method_trackable && (editing || !order.shipping_tracking_code)">
            <form @submit.prevent="submit" method="POST">
                <div class="mb-4">
                    <label for="shipping_tracking_code">Numer przesyłki</label>
                    <Input type="text" v-model="form.shipping_tracking_code" id="shipping_tracking_code"/>
                </div>
                <div class="flex gap-2 justify-end">
                    <Button type="ghost" role="button" v-if="editing" @click="editing = false">Anuluj</Button>
                    <Button role="submit">Zapisz</Button>
                </div>
            </form>
        </div>
    </div>
</template>