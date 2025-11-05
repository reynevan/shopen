<script setup>

import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import Heading from "../components/Heading.vue";
import {Head, useForm} from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {useConfirm} from "@shopen/composables/useConfirm.js";
import Input from "@shopen/components/frontend/input/Input.vue";
import Checkbox from "../../../../components/frontend/input/Checkbox.vue";

defineOptions({layout: UserPanelLayout})

const props = defineProps({
    newsletter_active: {type: Boolean},
})

const auth = useAuthStore();

const form = useForm({
    first_name: auth.user.first_name,
    last_name: auth.user.last_name,
    email: auth.user.email,
    password: '',
    new_password: '',
    newsletter_active: props.newsletter_active,
});

const submit = () => {
    form.post(route('user.settings.update'), {
        preserveScroll: true,
    })
}

const { confirm } = useConfirm();

const removeAccount = async () => {
    const isConfirmed = await confirm({
        title: 'Potwierdź usunięcie',
        message: 'Czy na pewno chcesz usunąć konto?',
        confirmButtonText: 'Tak, usuń',
        cancelButtonText: 'Anuluj'
    });
    if (!isConfirmed) {
        return;
    }
}
</script>

<template>
    <Head title="Ustawienia konta"/>
    <Heading title="Ustawienia konta"/>
    <div>
        <section class="w-full lg:w-1/2 pb-6 mb-6 border-b border-light">
            <h2 class="text-2xl mb-4">Dane konta</h2>
            <form @submit.prevent="submit">
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                    <div class="w-full sm:w-1/2">
                        <FormField label="Imię" field="first_name" :error="form.errors.first_name">
                            <Input v-model="form.first_name"/>
                        </FormField>
                    </div>
                    <div class="w-full sm:w-1/2">
                        <FormField label="Nazwisko" field="last_name" :error="form.errors.last_name">
                            <Input v-model="form.last_name"/>
                        </FormField>
                    </div>
                </div>

                <FormField label="E-mail" field="email" :error="form.errors.email">
                    <Input type="email" v-model="form.email"/>
                </FormField>

                <FormField field="newsletter_active">
                    <Checkbox id="newsletter_active"
                              v-model="form.newsletter_active"
                              label="Zapisz do newslettera"/>
                </FormField>

                <FormField label="Nowe hasło" field="new_password" :error="form.errors.new_password">
                    <Input id="new_password"
                           v-model="form.new_password"
                           type="password"
                           name="new-password"/>
                </FormField>

                <FormField label="Aktuale hasło" field="password" :error="form.errors.password" required>
                    <Input id="password"
                           v-model="form.password"
                           type="password"
                           name="password"
                           required autocomplete="current-password"/>
                </FormField>

                <div class="flex justify-center mt-4">
                    <Button type="primary" role="submit" size="lg">
                        Zapisz zmiany
                    </Button>
                </div>

            </form>
        </section>
        <section class="w-full lg:w-1/2">
            <h2 class="text-2xl mb-4">Usuwanie konta</h2>
            <p class="mb-4 font-light">
                Usunięcie konta spowoduje trwałe usunięcie Twoich danych oraz historii zamówień. Tej operacji nie można
                cofnąć.
            </p>
            <div class="flex justify-center mt-4">
                <Button type="primary" @click="removeAccount" size="lg">
                    Usuń konto
                </Button>
            </div>
        </section>
    </div>
</template>
