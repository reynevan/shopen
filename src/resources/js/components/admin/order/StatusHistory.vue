<script setup>
    import {useForm} from "@inertiajs/vue3";

    const props = defineProps(['statusItems', 'orderStatuses', 'order']);

    const form = useForm({
        status: props.order.status,
        comment: ''
    })

    const submit = () => {
        form.post(route('admin.orders.updateStatus', props.order.id), {
            preserveState: true,
            preserveScroll: true
        })
    }
</script>

<template>
    <div>
        <form @submit.prevent="submit">
            <div>
                <label for="status">Status</label>
                <select name="status" id="status" v-model="form.status">
                    <option v-for="(label, value) in orderStatuses" :value="value">
                        {{ label }}
                    </option>
                </select>
            </div>
            <div>
                <label for="comment">Komentarz</label>
                <textarea class="block w-full" id="comment" name="comment" v-model="form.comment"></textarea>
            </div>
            <div>
                <input type="checkbox" name="email_notification" id="email_notification">
                <label for="email_notification">Powiadom klienta przez e-mail</label>
            </div>
            <div>
                <button type="submit">Zapisz</button>
            </div>
        </form>

        <div class="mb-2" v-for="statusItem in statusItems">
            <div class="flex items-center gap-2">
                <div class="font-bold ">{{ statusItem.status_label }}</div>
                <div class="">{{ statusItem.time_formatted }}</div>
                <div class="" v-if="statusItem.email_notification">Powiadomienie wysłane</div>
            </div>
            <div class="" v-if="statusItem.comment">
                {{ statusItem.comment }}
            </div>
        </div>
    </div>
</template>