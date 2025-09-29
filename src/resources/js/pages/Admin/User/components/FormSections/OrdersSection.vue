<script setup>
    import {Link} from "@inertiajs/vue3";
    import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";

    defineProps({
        orders: {type: Array},
    })
</script>

<template>
    <table class="w-full table-primary">
        <thead>
        <tr>
            <th class="text-left">Numer</th>
            <th class="text-right">Status</th>
            <th class="text-right">Wartość</th>
            <th class="text-right">Akcja</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="order in orders">
            <td class="text-left">{{ order.order_number }}</td>
            <td class="text-right">{{ order.status_label }}</td>
            <td class="text-right">{{ order.total_amount }}</td>
            <td class="text-right">
                <Link :href="route('admin.orders.show', order.id)">
                    <ActionButton type="view"/>
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
</template>