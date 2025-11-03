<script setup>

import {useCheckoutStore} from "@shopen/stores/checkout.js";
import {ref} from "vue";
import IconPercent from "@shopen/components/icons/IconPercent.vue";
import IconChevron from "@shopen/components/icons/IconChevron.vue";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import IconX from "@shopen/components/icons/IconX.vue";
import {router, useForm} from '@inertiajs/vue3';
import Input from "../../../../components/frontend/input/Input.vue";
import Button from "../../../../components/frontend/ui/Button.vue";

const props = defineProps(['promoCode']);

const checkout = useCheckoutStore();

const loading = ref(false);
const showForm = ref(false);
const error = ref(false);

const form = useForm({
    code: props.promoCode ? props.promoCode : null
});

const toggleForm = () => {
    showForm.value = !showForm.value;
}

const deleteCode = () => {
    form.reset();
    router.put(route('checkout.update-promo-code'), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['promoCode', 'summary']
    })
}

const submit = () => {
    if (loading.value || !form.code) {
        return;
    }

    form.put(route('checkout.update-promo-code'), {
        preserveState: true,
        preserveScroll: true,
        only: ['promoCode', 'summary']
    })

}

</script>

<template>
    <div class="py-4">
        <div v-if="!promoCode">
            <div @click="toggleForm"
                 class="flex items-center justify-between py-2 px-4 hover:bg-accent/10 transition-colors cursor-pointer rounded">
                <div class="flex items-center justify-start">
                    <IconPercent/>
                    <div class="ml-2">Masz kod promocyjny?</div>
                </div>
                <IconChevron down v-if="!showForm"/>
                <IconChevron up v-if="showForm"/>
            </div>

            <div v-if="showForm" class="mt-2">
                <FormField label-for="promocode">
                    <form @submit.prevent="submit">
                        <div class="flex items-stretch justify-between">
                            <Input class="mr-2" id="promocode" v-model="form.code"/>
                            <Button
                                type="primary"
                                role="submit"
                                :disabled="loading">
                                <span v-if="!loading">Aktywuj</span>
                                <span v-if="loading">
                                    <IconLoader></IconLoader>
                                </span>
                            </Button>
                        </div>
                    </form>
                    <p v-if="form.errors.code" class="error-msg">
                        {{ form.errors.code }}
                    </p>
                </FormField>
            </div>
        </div>
        <div v-else>
            <div class="flex items-center justify-between">
                <div>Kod promocyjny</div>
                <div class="bg-accent/20 text-accent-600 flex items-stretch">
                    <div class="py-1 px-2">
                        {{ promoCode }}
                    </div>
                    <div>
                        <button @click="deleteCode"
                                class="flex items-center justify-center px-1 cursor-pointer h-full hover:bg-accent/30 transition-colors">
                            <IconX md/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>