<script setup>

import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
import {router, usePage} from "@inertiajs/vue3";
import IconEdit from "../../../../components/icons/IconEdit.vue";
import IconTrash from "../../../../components/icons/IconTrash.vue";
import {useConfirm} from "@shopen/composables/useConfirm.js";

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
const { confirm } = useConfirm();

const selectAddress = () => {
    if (!props.address.id) {
        return;
    }
    router.put(route('api.users.addresses.update-default', props.address.id), {}, {
        preserveState: true,
        preserveScroll: true
    })
}

const edit = () => {
    emits('onEdit', props.address);
}

const remove = async () => {
    const isConfirmed = await confirm({
        title: 'Potwierdź usunięcie',
        message: 'Czy na pewno chcesz usunąć ten adres?',
        confirmButtonText: 'Tak, usuń',
        cancelButtonText: 'Anuluj',
        confirmButtonType: 'danger'
    });
    if (!isConfirmed) {
        return;
    }
    router.delete(route('api.users.addresses.destroy', props.address.id), {
        preserveState: true,
        preserveScroll: true
    });
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
                    @click="remove">
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
                <IconCheckCircle/>
                Ustaw jako domyślny
            </button>
        </div>
    </div>
</template>