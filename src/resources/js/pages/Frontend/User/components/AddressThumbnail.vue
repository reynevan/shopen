<script setup>

import IconCircleCheck from "@shopen/components/icons/IconCircleCheck.vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {useCoverStore} from "@shopen/stores/cover.js";
import IconEdit from "../../../../components/icons/IconEdit.vue";
import IconTrash from "../../../../components/icons/IconTrash.vue";

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
const page = usePage()


const selectAddress = () => {
    if (!props.address.id) {
        return;
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
    <div class="py-4 text-lg relative flex flex-col justify-between">
        <div class="px-6 mb-4">
            <div class="font-semibold">{{ address.first_name }} {{ address.last_name }}</div>
            <div class="" v-if="address.company">{{ address.company }}</div>
            <div class="" v-if="address.company_nip">NIP: {{ address.company_nip }}</div>
            <div class="">{{ address.address_line }}</div>
            <div class="">{{ address.postal_code }} {{ address.city }}</div>
            <div class="" v-if="address.phone">tel. {{ address.phone }}</div>
            <div class="" v-if="address.email">{{ address.email }}</div>
        </div>
        <div class="px-2">
            <div class="flex gap-4 sm:gap-2 items-center justify-start mb-2">
                <button
                    class="text-link hover:text-red-800 hover:bg-red-100 rounded-lg text-sm flex items-center gap-2 cursor-pointer py-2 px-4 justify-self-start hover:text-black transition-colors"
                    @click="edit">
                    <IconTrash/>
                    Usuń
                </button>
                <button
                    class="text-link hover:text-link-hover hover:bg-link-100 rounded-lg text-sm flex items-center gap-2 cursor-pointer py-2 px-4 justify-self-start hover:text-black transition-colors"
                    @click="edit">
                    <IconEdit/>
                    Edytuj
                </button>
            </div>

            <button v-if="selectable"
                    class="text-link cursor-pointer hover:text-accent-600 hover:bg-accent-100 rounded-lg text-sm transition-colors flex items-center gap-2 py-2 px-4"
                    @click="selectAddress">
                <IconCircleCheck/>
                Ustaw jako domyślny
            </button>
        </div>
    </div>
</template>