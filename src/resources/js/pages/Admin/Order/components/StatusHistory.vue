<script setup>
import {useForm} from "@inertiajs/vue3";
import Select from "@shopen/components/admin/form/input/Select.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

const props = defineProps(['statusItems', 'orderStatuses', 'order']);

const form = useForm({
    status: props.order.status,
    email_notification: false,
    comment: ''
})

const submit = () => {
    form.post(route('admin.orders.update-status', props.order.id), {
        preserveState: true,
        preserveScroll: true
    })
    form.reset()
}

const statusOptions = [];
for (let id in props.orderStatuses) {
    statusOptions.push({id, value: props.orderStatuses[id] });
}
</script>

<template>
    <div>
        <form @submit.prevent="submit">
            <div class="mb-2">
                <label for="status">Status</label>
                <Select id="status" v-model="form.status" :options="statusOptions"/>
            </div>
            <div class="mb-2">
                <label for="comment">Komentarz</label>
                <textarea class="block w-full input py-2" rows="4" id="comment" name="comment"
                          v-model="form.comment"></textarea>
            </div>
            <div class="flex items-center gap-2 mb-4">
                <Toggle v-model="form.email_notification" id="notify_user"/>
                <label for="notify_user">Powiadom kupującego przez email</label>
            </div>
            <div>
                <Button role="submit">Zapisz</Button>
            </div>
        </form>

        <div v-if="statusItems?.length" class="pt-4 mt-4 border-t">
            <div class="text-sm uppercase mb-2 pb-2 inline-block border-b">Historia zmian statusu</div>
            <div class="space-y-4">
                <div v-for="statusItem in statusItems">
                    <div class="flex items-center gap-2">
                        <div class="font-bold ">{{ statusItem.status_label }}</div>
                        <div class="">{{ statusItem.time_formatted }}</div>
                        <div class="" v-if="statusItem.email_notification">
                            <i title="Email z powiadomieniem wysłany" class="bi bi-send-check"></i>
                        </div>
                    </div>
                    <div class="" v-if="statusItem.comment">
                        {{ statusItem.comment }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>