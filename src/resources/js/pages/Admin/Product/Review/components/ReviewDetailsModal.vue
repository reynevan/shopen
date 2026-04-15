<script setup>
import BaseModal from "@shopen/components/admin/ui/BaseModal.vue";
import ProductThumbnailImage from "@shopen/components/admin/product/ProductThumbnailImage.vue";
import RatingDisplay from "./RatingDisplay.vue";
import {ref} from "vue";
import ActionButton from "../../../../../components/admin/ui/ActionButton.vue";

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
            <div class="bg-neutral-100 rounded-lg px-4 py-4 mb-6">
                <div class="flex items-center gap-4">
                    <a :href="review.product.url" target="_blank" class="flex-shrink-0">
                        <ProductThumbnailImage :product="review.product" size="sm"/>
                    </a>
                    <div>
                        <a :href="review.product.url" target="_blank" class="text-neutral-800">
                            {{ review.product.name }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informacje o autorze -->
            <div class="p-4 mb-6">
                <div class="flex justify-between gap-4 divide-x divide-x-border-light">
                    <div class="w-1/2 px-2">
                        <span class="text-sm text-neutral-500">Autor</span>
                        <p>{{ review.user.first_name }} {{ review.user.last_name }}</p>
                    </div>
                    <div class="w-1/2 px-2">
                        <span class="text-sm text-neutral-500">Data dodania</span>
                        <p>{{ review.created_at }}</p>
                    </div>
                </div>
            </div>

            <!-- Porównanie wersji -->
            <div v-if="review.comment_to_verify" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Poprzednia wersja -->
                <div class="p-5 border border-light rounded">
                    <h3 class="font-semibold text-neutral-700 mb-4 flex items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        Poprzednia wersja
                    </h3>
                    <div class="p-4 mb-4">
                        <RatingDisplay :rating="review.rating"/>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="whitespace-pre-wrap text-neutral-700 leading-relaxed">{{ review.comment }}</p>
                    </div>
                </div>

                <div class="p-5 border border-strong rounded">
                    <h3 class="font-semibold text-neutral-700 mb-4 flex items-center gap-2">
                        <i class="bi bi-pencil-square"></i>
                        Edytowana wersja
                    </h3>
                    <div class="p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <RatingDisplay :rating="review.rating_to_verify"/>
                            <span v-if="review.rating_to_verify !== review.rating"
                                  class="flex items-center gap-1 text-sm font-medium">
                                <span :class="{
                                    'text-green-600': review.rating_to_verify > review.rating,
                                    'text-red-600': review.rating_to_verify < review.rating
                                }">
                                    <span>{{ review.rating_to_verify - review.rating > 0 ? '+' : '' }}</span>{{ review.rating_to_verify - review.rating }}<i class="bi bi-star-fill"></i>
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

                <div class="flex-1 flex divide-x divide-x-border-light">

                    <ActionButton type="cancel"
                                  size="md"
                                  @click="cancel"
                                  :loading="loading"
                                  v-if="review.status !== 'rejected'">
                        Odrzuć
                    </ActionButton>

                    <ActionButton type="remove"
                                  size="md"
                                  @click="cancel"
                                  :loading="loading">
                        Usuń
                    </ActionButton>
                </div>

                <div class="flex gap-3 flex-wrap justify-end">
                    <ActionButton type="accept"
                            size="md"
                            @click="accept"
                            :loading="loading"
                            v-if="review.status !== 'approved'">
                        Zaakceptuj
                    </ActionButton>
                </div>
            </div>
        </template>
    </BaseModal>
</template>