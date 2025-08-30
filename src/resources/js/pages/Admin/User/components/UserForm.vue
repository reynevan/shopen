<script setup>
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import {useForm, Link} from "@inertiajs/vue3";
import FormMenu from "@shopen/components/admin/form/menu/FormMenu.vue";
import {ref} from "vue";


const props = defineProps({
    user: {type: Object, required: true},
    orders: {type: Array},
})

const form = useForm({
    first_name: props.user?.first_name,
    last_name: props.user?.last_name,
    email: props.user?.email,
})

const sections = [
    {
        section: 'general',
        title: 'Główne'
    },
    {
        section: 'addresses',
        title: 'Adresy'
    },
    {
        section: 'orders',
        title: 'Zamówienia'
    }
]

const activeSection = ref('general');

const onChangeSection = (section) => {
    activeSection.value = section;
}

const save = () => {

}
</script>

<template>
    <ActionsPanel back-route="admin.users.index">
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div class="flex items-start gap-6">
        <div class="sticky top-12">
            <FormMenu :sections="sections" @onSelect="onChangeSection"/>
        </div>
        <div class="border-l border-light pl-6 w-full">

            <section v-show="activeSection === 'general'">
                <FormField
                    :required="true"
                    :error="form.errors.first_name"
                    label="Imię" label-for="first_name">
                    <Input v-model="form.first_name" id="first_name"/>
                </FormField>

                <FormField
                    :required="true"
                    :error="form.errors.last_name"
                    label="Nazwisko" label-for="last_name">
                    <Input v-model="form.last_name" id="last_name"/>
                </FormField>

                <FormField
                    :required="true"
                    :error="form.errors.email"
                    label="Email" label-for="email">
                    <Input v-model="form.email" type="email" id="email"/>
                </FormField>
            </section>

            <section v-show="activeSection === 'orders'">
                <table class="w-full">
                    <thead>
                    <tr>
                        <th>Numer</th>
                        <th>Status</th>
                        <th>Wartość</th>
                        <th>Akcja</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="order in orders">
                        <td> {{ order.order_number }}</td>
                        <td>{{ order.status_label }}</td>
                        <td>{{ order.total_amount }}</td>
                        <td>
                            <Link :href="route('admin.orders.show', order.id)">Szczegóły</Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

        </div>
    </div>

</template>