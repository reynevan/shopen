<script setup>
import { ref } from 'vue'
import {router, useForm} from '@inertiajs/vue3'
import {Link} from "@inertiajs/vue3";
import Checkbox from "../input/Checkbox.vue";
import IconMail from "../../icons/IconMail.vue";

const form = useForm({
    email: '',
    privacy_accepted: false,
    attributes: {}
})

const processing = ref(false)
const message = ref('')
const success = ref(false)
const errors = ref({})

const subscribe = () => {
    processing.value = true
    errors.value = {}
    message.value = ''

    form.post(route('newsletter.subscribe'), {
        onSuccess: (response) => {
            success.value = true
            message.value = response.props.flash?.message || 'Dziękujemy za subskrypcję!'
            form.value.email = ''
            form.value.privacy_accepted = false
        },
        onError: (responseErrors) => {
            success.value = false
            errors.value = responseErrors
            message.value = 'Wystąpił błąd. Sprawdź dane.'
        },
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>
<template>
    <form @submit.prevent="subscribe" class="newsletter w-full max-w-md mx-auto space-y-4">
        <!-- Email -->
        <div class="newsletter-input-wrapper">
            <div class="mail-icon">
                <IconMail/>
            </div>
            <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="Twój adres e-mail"
                class="newsletter-input"
                :class="{ 'border-red-500': errors.email }"
                required
                autocomplete="email"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                {{ errors.email }}
            </p>
        </div>

        <!-- Submit -->
        <button
            type="submit"
            class="newsletter-button"
            :disabled="processing"
        >
            <span v-if="processing">Zapisuję...</span>
            <span v-else>Zapisz się</span>
        </button>

        <!-- Messages -->
        <div v-if="message" class="mt-4">
            <div
                :class="[
                  'px-4 py-3 rounded-md text-sm font-medium',
                  success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
            >
                {{ message }}
            </div>
        </div>
    </form>
</template>
