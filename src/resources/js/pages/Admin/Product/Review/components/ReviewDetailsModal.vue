<script setup>
import BaseModal from "@shopen/components/admin/ui/BaseModal.vue";
import ProductThumbnailImage from "@shopen/components/admin/product/ProductThumbnailImage.vue";
import RatingDisplay from "./RatingDisplay.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import {ref} from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    review: {
        type: Object,
        default: {}
    }
})

const emits = defineEmits(['onClose', 'onAccept', 'onCancel', 'onRemove']);

const loading = ref(false);

const accept = () => {
    loading.value = true;
    emits('onAccept', props.review)
}

const cancel = () => {
    loading.value = true;
    emits('onCancel', props.review)
}

const remove = () => {
    loading.value = true;
    emits('onRemove', props.review)
}
</script>

<template>
    <BaseModal :show="show" @onClose="emits('onClose')" class="w-full max-w-5xl">
        <template #header>
            <div class="flex items-center gap-2">
                <span class="text-lg font-semibold">Podgląd opinii</span>
                <span class="text-sm text-neutral-500">#{{ review.id }}</span>
            </div>
        </template>
        <template #default>
            <!-- Sekcja produktu -->
            <div class="bg-neutral-50 rounded-lg px-4 pb-4 mb-6">
                <div class="flex items-center gap-4">
                    <a :href="review.product.url" target="_blank" class="flex-shrink-0">
                        <ProductThumbnailImage :product="review.product" size="sm"/>
                    </a>
                    <div>
                        <a :href="review.product.url" target="_blank"
                           class="text-neutral-800 font-medium hover:text-blue-600 transition-colors">
                            {{ review.product.name }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informacje o autorze -->
            <div class="bg-white border border-neutral-200 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <span class="text-sm text-neutral-500">Autor:</span>
                        <p class="font-medium">{{ review.user.first_name }} {{ review.user.last_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-neutral-500">Data dodania:</span>
                        <p class="font-medium">{{ review.created_at }}</p>
                    </div>
                </div>
            </div>

            <!-- Porównanie wersji -->
            <div v-if="review.comment_to_verify" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Poprzednia wersja -->
                <div class="bg-neutral-50 rounded-lg p-5">
                    <h3 class="font-semibold text-neutral-700 mb-4 flex items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        Poprzednia wersja
                    </h3>
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <RatingDisplay :rating="review.rating" class="justify-center"/>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="whitespace-pre-wrap text-neutral-700 leading-relaxed">{{ review.comment }}</p>
                    </div>
                </div>

                <div class="bg-neutral-50 rounded-lg p-5 border-1 border-strong">
                    <h3 class="font-semibold text-neutral-700 mb-4 flex items-center gap-2">
                        <i class="bi bi-pencil-square"></i>
                        Edytowana wersja
                    </h3>
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-center gap-3">
                            <RatingDisplay :rating="review.rating_to_verify"/>
                            <span v-if="review.rating_to_verify !== review.rating"
                                  class="flex items-center gap-1 text-sm font-medium">
                                <i v-if="review.rating_to_verify > review.rating"
                                   class="bi bi-arrow-up-circle-fill text-green-500 text-lg"></i>
                                <i v-else
                                   class="bi bi-arrow-down-circle-fill text-red-500 text-lg"></i>
                                <span :class="{
                                    'text-green-600': review.rating_to_verify > review.rating,
                                    'text-red-600': review.rating_to_verify < review.rating
                                }">
                                    {{ Math.abs(review.rating_to_verify - review.rating) }} pkt
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="whitespace-pre-wrap text-neutral-700 leading-relaxed">{{ review.comment_to_verify }}</p>
                    </div>
                </div>
            </div>

            <!-- Pojedyncza opinia -->
            <div v-else class="bg-white border border-neutral-200 rounded-lg p-6">
                <div class="mb-4">
                    <RatingDisplay :rating="review.rating"/>
                </div>
                <p class="whitespace-pre-wrap text-neutral-700 leading-relaxed">{{ review.comment }}</p>
            </div>
        </template>

        <template #buttons>
            <div class="flex flex-col sm:flex-row gap-3 w-full">
                <!-- Grupa przycisków po lewej -->
                <div class="flex-1 flex gap-3">
                    <Button type="neutral" @click="emits('onClose')" size="md">
                        <i class="bi bi-x-lg mr-2"></i>
                        Zamknij
                    </Button>
                </div>

                <!-- Grupa przycisków po prawej -->
                <div class="flex gap-3 flex-wrap justify-end">
                    <Button type="success"
                            size="md"
                            @click="accept"
                            :loading="loading"
                            v-if="review.status !== 'approved'">
                        <i class="bi bi-check-lg mr-2"></i>
                        Zaakceptuj
                    </Button>
                    <Button type="warning"
                            size="md"
                            @click="cancel"
                            :loading="loading"
                            v-if="review.status !== 'rejected'">
                        <i class="bi bi-x-lg mr-2"></i>
                        Odrzuć
                    </Button>
                    <Button type="danger"
                            size="sm"
                            variant="outline"
                            @click="remove"
                            :loading="loading">
                        <i class="bi bi-trash3"></i>
                    </Button>
                </div>
            </div>
        </template>
    </BaseModal>
</template>