<script setup>
import BaseModal from "@shopen/components/frontend/ui/BaseModal.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {useForm} from "@inertiajs/vue3";
import ProductThumbnailImage from "@shopen/components/frontend/product/ProductThumbnailImage.vue";
import RatingSelector from "./RatingSelector.vue";
import {computed, watch} from "vue";

const props = defineProps({
    'show': {
        type: Boolean,
        default: false
    },
    'product': {
        type: Object,
        default: {}
    },
    'review': {
        type: Object,
        default: {}
    }
})

const emits = defineEmits(['onClose']);


const initialFormState = {
    id: null,
    rating: 0,
    comment: '',
};

const form = useForm({...initialFormState});

const reviewLength = computed(() => form.comment.length)

const submit = () => {
    if (props.review && props.review.id) {
        form.put(route('products.reviews.update', props.review.id), {
            onSuccess: () => {
                form.reset()
                emits('onClose')
            },
            only: ['product', 'reviews', 'flash', 'errors'],
            preserveScroll: true
        })
    } else {
        form.post(route('products.reviews.store', props.product.id), {
            onSuccess: () => {
                form.reset()
                emits('onClose')
            },
            only: ['product', 'reviews', 'reviewSubmitted', 'flash', 'errors'],
            preserveScroll: true
        });
    }
};

watch(() => props.review, (newReview) => {
    form.defaults({...initialFormState});
    form.reset();

    if (newReview) {
        form.defaults(newReview);
        form.reset();
    }

    form.clearErrors();
}, {deep: true});
</script>

<template>

    <BaseModal :show="show" @onClose="emits('onClose')" class="w-full max-w-2xl">
        <template #header>
            <span v-if="!review || !review.id">Dodaj opinię</span>
            <span v-else>Edytuj opinię</span>
        </template>
        <template #default>
            <div class="">
                <div class="flex items-center border-b pb-2 mb-2">
                    <div class="mr-2">
                        <ProductThumbnailImage :product="product" size="sm"/>
                    </div>
                    <div class="text-lg text-neutral-700">{{ product.attributes.name }}</div>
                </div>
                <div class="py-4 mb-4 flex flex-col items-center justify-center">
                    <div class="mb-2">Twoja ocena</div>
                    <RatingSelector v-model="form.rating"/>
                </div>
                <form @submit.prevent="submit">
                    <div class="mb-8">
                        <div class="mb-2">
                            <label>Co sądzisz o tym produkcie? <span class="text-neutral-500">(opcjonalnie)</span></label>
                        </div>
                        <textarea v-model="form.comment" maxlength="1000" rows="4"></textarea>
                        <div class="text-right text-sm text-neutral-600">
                            {{ reviewLength }}/1000
                        </div>
                        <div v-if="form.errors.comment">{{ form.errors.comment }}</div>
                    </div>
                    <div class="flex items-center gap-6">
                        <Button type="primary" role="submit" :disabled="form.processing">Wyślij opinię</Button>
                    </div>
                </form>
            </div>
        </template>
    </BaseModal>
</template>