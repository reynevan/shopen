<script setup>
import Button from "@shopen/components/admin/ui/Button.vue";
import {router} from "@inertiajs/vue3";
import Select from "@shopen/components/admin/form/input/Select.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";

const props = defineProps(['order', 'paymentStatuses']);


const submit = (payment) => {
    router.post(route('admin.orders.update-payment-status', [props.order.id, payment.id]), {
        status: payment.status
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const refreshStatus = (payment) => {
    router.post(route('admin.orders.refresh-payment-status', [props.order.id, payment.id]), {},
        {
            preserveScroll: true
        })
}

const statusOptions = [];
for (let id in props.paymentStatuses) {
    statusOptions.push({id, value: props.paymentStatuses[id]});
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <div>{{ order.payment_method_label }}</div>
        </div>
        <div class="divide-y space-y-2">
            <div v-for="payment in order.payments">
                <div class="flex items-start justify-between py-2">

                    <div class="flex flex-col items-start">
                        <div v-if="payment.is_return" class="text-xs">
                            <i class="bi bi-reply"></i> ZWROT
                        </div>
                        <div v-else class="text-xs">
                            <i class="bi bi-credit-card"></i> PŁATNOŚĆ
                        </div>
                        <div>{{ payment.amount }}</div>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="text-xs">status</div>
                        <div v-show="!payment.editing" class="flex items-center">
                            {{ payment.status_label }}
                            <ActionButton type="edit" @click="payment.editing = true"/>
                            <ActionButton type="reload" @click="refreshStatus(payment)" title="Odśwież status"/>
                        </div>
                        <div v-show="payment.editing">
                            <form method="POST">
                                <div class="mb-2">
                                    <Select id="status" v-model="payment.status" :options="statusOptions"/>
                                </div>
                                <div class="flex gap-2">
                                    <Button role="button" type="ghost" @click="() => payment.editing = false">
                                        Anuluj
                                    </Button>
                                    <Button role="button" @click="submit(payment)">Zapisz</Button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div v-if="payment.gateway_transaction_id" class="flex items-start justify-between pb-2">
                    <div>
                        <div class="text-xs">ID transakcji</div>
                        <div>{{ payment.gateway_transaction_id }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>