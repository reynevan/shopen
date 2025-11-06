<script setup>

import {router, usePage} from "@inertiajs/vue3";
import IconEdit from "../../../../components/icons/IconEdit.vue";
import IconTrash from "../../../../components/icons/IconTrash.vue";
import {useConfirm} from "@shopen/composables/useConfirm.js";
import IconHomeCheck from "../../../../components/icons/IconHomeCheck.vue";

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
        cancelButtonText: 'Anuluj'
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
    <div class="py-4 relative flex flex-col justify-between">
        <div class="address px-6 mb-4">
            <div class="font-semibold">{{ address.first_name }} {{ address.last_name }}</div>
            <div class="font-semibold" v-if="address.company">{{ address.company }}</div>
            <div class="address-line" v-if="address.company_nip">NIP: {{ address.company_nip }}</div>
            <div class="address-line">{{ address.address_line }}</div>
            <div class="address-line">{{ address.postal_code }} {{ address.city }}</div>
            <div class="address-line" v-if="address.phone">tel. {{ address.phone }}</div>
            <div class="address-line" v-if="address.email">{{ address.email }}</div>
        </div>
        <div class="px-2">
            <div class="address-buttons flex items-center justify-start mb-2">
                <button
                    class="delete-address-button"
                    @click="remove">
                    <IconTrash/>
                    Usuń
                </button>
                <button
                    class="edit-address-button"
                    @click="edit">
                    <IconEdit/>
                    Edytuj
                </button>
            </div>

            <button v-if="selectable"
                    class="default-address-button"
                    @click="selectAddress">
                <IconHomeCheck/>
                Ustaw jako domyślny
            </button>
        </div>
    </div>
</template>