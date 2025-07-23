<script setup>

import IconCircleCheck from "@shopen/components/icons/IconCircleCheck.vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {useCoverStore} from "@shopen/stores/cover.js";

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
    if (!props.address.id) {
        return;
    }
    if (props.address.type === 'shipping') {
        page.props.selectedShippingAddress = props.address.id;
        page.props.errors.shippingAddress = null;
    }
    else if (props.address.type === 'billing') {
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
         class="px-8 border-2 relative w-full sm:w-auto min-w-[220px] flex flex-col justify-between"
         :class="[
             isSelected ? 'border-accent' : 'border-transparent',
             selectable ? 'pt-10 pb-4' : 'py-4'
         ]"
    >
        <div class="absolute top-0 right-0 bg-accent text-white flex items-center justify-center p-2" v-if="selectable && isSelected">
            <IconCircleCheck></IconCircleCheck>
        </div>
        <div>
            <div class="">{{ address.first_name }} {{ address.last_name }}</div>
            <div class="" v-if="address.company">{{ address.company }}</div>
            <div class="" v-if="address.company_nip">NIP: {{ address.company_nip }}</div>
            <div class="">{{ address.address_line }}</div>
            <div class="">{{ address.postal_code }} {{ address.city }}</div>
            <div class="" v-if="address.phone">tel. {{ address.phone }}</div>
            <div class="" v-if="address.email">{{ address.email }}</div>
        </div>
        <div class="flex items-center justify-start">
            <button class="text-neutral-600 cursor-pointer py-2 pr-2 justify-self-start hover:text-black transition-colors"
                    @click="edit">Edytuj</button>

            <button v-if="selectable && !isSelected && auth.isLoggedIn"
                    class="text-accent-400 cursor-pointer border border-accent-400 ml-2 py-1 px-2 rounded justify-self-end font-semibold hover:text-accent-600 hover:border-accent-600 transition-colors"
                    @click="selectAddress">Wybierz</button>
        </div>
    </div>
</template>