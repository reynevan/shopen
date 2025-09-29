<script setup>
import { ref } from 'vue'
import {router, useForm} from '@inertiajs/vue3'


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
    <form @submit.prevent="subscribe" class="w-full max-w-md mx-auto space-y-4">
        <!-- Email -->
        <div>
            <label for="email" class="sr-only">Adres e-mail</label>
            <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="Twój adres e-mail"
                class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm px-3 py-2 shadow-sm"
                :class="{ 'border-red-500': errors.email }"
                required
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                {{ errors.email }}
            </p>
        </div>

        <!-- Privacy -->
        <div class="flex items-start">
            <input
                v-model="form.privacy_accepted"
                type="checkbox"
                id="privacy"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                required
            />
            <label for="privacy" class="ml-2 text-sm text-gray-700">
                Akceptuję
                <a href="/privacy-policy" target="_blank" class="text-indigo-600 hover:text-indigo-800">politykę prywatności</a>
            </label>
        </div>
        <p v-if="errors.privacy_accepted" class="mt-1 text-sm text-red-600">
            {{ errors.privacy_accepted }}
        </p>

        <!-- Submit -->
        <button
            type="submit"
            class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
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
