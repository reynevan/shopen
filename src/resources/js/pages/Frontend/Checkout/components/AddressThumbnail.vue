<script setup>

import {useAuthStore} from "@shopen/stores/auth.js";
import {computed} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {useCoverStore} from "@shopen/stores/cover.js";
import Dropdown from "../../../../components/frontend/ui/Dropdown.vue";
import IconDots from "../../../../components/icons/IconDots.vue";
import IconEdit from "../../../../components/icons/IconEdit.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";

const props = defineProps({
    address: {
        type: Object
    },
    selectable: {
        type: Boolean,
        default: true
    }
})
const emits = defineEmits(['onEdit'])
const auth = useAuthStore()
const cover = useCoverStore()
const page = usePage()

const isSelected = computed(() => {
    if (!props.selectable) return false;
    if (props.address.type === 'shipping') return page.props.selectedShippingAddress === props.address.id;
    if (props.address.type === 'billing') return page.props.selectedBillingAddress === props.address.id;
});

const selectAddress = () => {
    if (!props.address.id || !props.selectable) {
        return;
    }
    if (props.address.type === 'shipping') {
        page.props.selectedShippingAddress = props.address.id;
        page.props.errors.shippingAddress = null;
    } else if (props.address.type === 'billing') {
        page.props.selectedBillingAddress = props.address.id;
        page.props.errors.billingAddress = null;
    }
    const routeName = props.address.type === 'shipping' ? 'checkout.select-shipping-address' : 'checkout.select-billing-address';
    router.put(route(routeName), {
        'id': props.address.id,
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['addresses']
    })
}

const edit = () => {
    emits('onEdit', props.address);
}
</script>

<template>
    <div
        class="pl-4 pr-12 sm:pr-24 py-4 rounded relative w-full flex flex-col justify-between border transition-all duration-300"
        :class="[isSelected ? 'bg-accent/50' : 'border-light', selectable && !isSelected ? 'opacity-50 hover:opacity-100' : '']"
    >
        <div class="absolute right-1 top-2">
            <Button type="ghost" size="sm" title="Edytuj adres" @click="edit">
                <IconEdit size="xl"/> <span class="text-sm ml-2 hidden sm:inline">Edytuj</span>
            </Button>
        </div>
        <div :class="[selectable ? 'cursor-pointer' : '']"
             @click="selectAddress"
             :title="selectable ? (isSelected ? 'Wybrany adres' : 'Wybierz adres') : ''"
        >
            <div class="font-semibold">{{ address.first_name }} {{ address.last_name }}</div>
            <div class="font-semibold" v-if="address.company">{{ address.company }}</div>
            <div class="" v-if="address.company_nip">NIP: {{ address.company_nip }}</div>
            <div class="">{{ address.address_line }}</div>
            <div class="">{{ address.postal_code }} {{ address.city }}</div>
            <div class="" v-if="address.phone">tel. {{ address.phone }}</div>
            <div class="" v-if="address.email">{{ address.email }}</div>
        </div>
    </div>
</template>