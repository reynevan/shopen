<script setup>
import Button from "@shopen/components/admin/ui/Button.vue";
import {router} from "@inertiajs/vue3";
import Select from "@shopen/components/admin/form/input/Select.vue";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";

const props = defineProps(['order', 'paymentStatuses']);


const submit = (paymentId) => {
    router.post(route('admin.orders.payment', props.order.id, paymentId), {
        preserveState: true,
        preserveScroll: true
    })
}
const statusOptions = [];
for (let id in props.paymentStatuses) {
    statusOptions.push({id, value: props.paymentStatuses[id] });
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <div>{{ order.payment_method_label }}</div>
        </div>
        <div class="divide-y space-y-2">
            <div v-for="payment in order.payments" class="flex items-start justify-between py-2">
                <div class="flex flex-col items-start">
                    <div class="text-xs">kwota</div>
                    <div>{{ payment.amount }}</div>
                </div>
                <div class="flex flex-col items-end">
                    <div class="text-xs">status</div>
                    <div v-show="!payment.editing" class="flex items-center">
                        {{ payment.status_label }}
                        <ActionButton type="edit" @click="() => payment.editing = true" />
                    </div>
                    <div v-show="payment.editing">
                        <form @submit.prevent="submit" method="POST">
                            <div class="mb-2">
                                <Select id="status" v-model="payment.status" :options="statusOptions"/>
                            </div>
                            <Button role="submit">Zapisz</Button>
                            <Button role="button" type="ghost" @click="() => payment.editing = false" >Anuluj</Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>