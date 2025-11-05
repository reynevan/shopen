<script setup>
import {useForm, Link} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import {trackLogin} from "../../../utils/ga4";
import Input from "../input/Input.vue";
import Checkbox from "../input/Checkbox.vue";

const props = defineProps({
    redirectTo: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    redirectTo: props.redirectTo,
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => {
            console.log("login success");
            trackLogin()
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit">

        <FormField label="Email" field="email" :error="form.errors.email">
            <Input v-model="form.email" required autofocus/>
        </FormField>

        <FormField label="Hasło" field="password" :error="form.errors.password">
            <Input id="password"
                   v-model="form.password"
                   type="password"
                   name="password"
                   required autocomplete="current-password"/>
        </FormField>

        <div class="block form-field">
            <label for="remember_me" class="inline-flex items-center">
                <Checkbox id="remember_me"
                          v-model="form.remember"
                          label="Zapamiętaj mnie"
                          name="remember"/>
            </label>
        </div>

        <div class="mt-4">
            <div>
                <Link class="link-primary text-sm"
                      :href="route('password.remind')">
                    Nie pamiętasz hasła?
                </Link>
            </div>

            <div class="flex justify-center mt-4">
                <Button type="primary" size="lg" role="submit" full-width>
                    Zaloguj się
                </Button>
            </div>
        </div>
    </form>
</template>