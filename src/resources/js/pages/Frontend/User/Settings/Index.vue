<script setup>

import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import Heading from "../components/Heading.vue";
import {Head, useForm} from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {useConfirm} from "@shopen/composables/useConfirm.js";

defineOptions({layout: UserPanelLayout})

const auth = useAuthStore();

const form = useForm({
    first_name: auth.user.first_name,
    last_name: auth.user.last_name,
    email: auth.user.email,
    password: '',
    new_password: '',
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
        cancelButtonText: 'Anuluj',
        confirmButtonType: 'danger'
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
                            <input type="text" v-model="form.first_name">
                        </FormField>
                    </div>
                    <div class="w-full sm:w-1/2">
                        <FormField label="Nazwisko" field="last_name" :error="form.errors.last_name">
                            <input type="text" v-model="form.last_name">
                        </FormField>
                    </div>
                </div>

                <FormField label="E-mail" field="email" :error="form.errors.email">
                    <input type="email" v-model="form.email">
                </FormField>

                <FormField label="Nowe hasło" field="new_password" :error="form.errors.new_password">
                    <input id="new_password"
                           v-model="form.new_password"
                           type="password"
                           name="new-password"/>
                </FormField>

                <FormField label="Aktuale hasło" field="password" :error="form.errors.password" required>
                    <input id="password"
                           v-model="form.password"
                           type="password"
                           name="password"
                           required autocomplete="current-password"/>
                </FormField>

                <div class="flex justify-center mt-4">
                    <Button type="secondary" role="submit">
                        Zapisz zmiany
                    </Button>
                </div>

            </form>
        </section>
        <section class="w-full lg:w-1/2">
            <h2 class="text-2xl mb-4">Usuwanie konta</h2>
            <p class="mb-4">
                Usunięcie konta spowoduje trwałe usunięcie Twoich danych oraz historii zamówień. Tej operacji nie można
                cofnąć.
            </p>
            <div class="flex justify-center mt-4">
                <Button type="primary" @click="removeAccount">
                    Usuń konto
                </Button>
            </div>
        </section>
    </div>
</template>
