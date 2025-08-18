<script setup>
import ReviewModal from "@shopen/pages/Frontend/Product/components/ReviewModal.vue";
import {ref, watch} from "vue";
import ReviewItem from "./ReviewItem.vue";
import axios from "axios";
import Button from "@shopen/components/frontend/ui/Button.vue"
import RatingDisplay from "@shopen/components/frontend/product/RatingDisplay.vue";
import {router, usePage} from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";

const props = defineProps(['product', 'reviews', 'reviewSubmitted', 'sort'])
const page = usePage();

const reviewsData = ref(props.reviews);
const reviewToEdit = ref(null);
const showReviewModal = ref(false);
const isLoading = ref(false);

const sort = ref(props.sort ?? null)

const sortOptions = [
    {label: 'Najbardziej pomocne', value: null},
    {label: 'Najnowsze', value: 'najnowsze'},
    {label: 'Ocena: od najwyższej', value: 'najwyzsza-ocena'},
    {label: 'Ocena: od najniższej', value: 'najnizsza-ocena'}
];

watch(() => props.reviews, (newReviews) => {
    reviewsData.value = newReviews;
});

const openAddReviewModal = () => {
    showReviewModal.value = true;
}

const openEditReviewModal = (review) => {
    reviewToEdit.value = review;
    showReviewModal.value = true;
}

const closeReviewModal = () => {
    showReviewModal.value = false;
    reviewToEdit.value = null;
}

const onSortChange = () => {
    const url = new URL(window.location.href);

    if (sort.value) {
        url.searchParams.set('opinie', sort.value);
    } else {
        url.searchParams.delete('opinie');
    }

    router.visit(url.toString(),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['reviews', 'sort'],
            onSuccess: () => {
                const element = document.getElementById('reviews-list');
                if (element) {
                    element.scrollIntoView({behavior: 'smooth'});
                }
            },
        }
    );
}

const loadMoreReviews = async () => {
    if (reviewsData.value.meta.current_page >= reviewsData.value.meta.last_page) return;

    isLoading.value = true;

    try {
        const response = await axios.get(route('api.products.reviews.index', props.product.id), {
            params: {
                page: reviewsData.value.meta.current_page + 1,
                opinie: sort.value
            }
        });

        reviewsData.value.data.push(...response.data.data);
        reviewsData.value.meta.current_page = response.data.meta.current_page;
    } catch (error) {
        console.error('Error loading reviews:', error);
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div class="review-list mb-12">

        <div v-if="!reviewsData.data.length" class="flex justify-center">
            <div class="border border-light rounded text-center py-4 px-6 sm:max-w-sm">
                <div class="font-semibold text-lg">
                    Jeszcze nikt nie ocenił tego produktu
                </div>
                <div class="my-4 color-neutral-600">
                    Bądź pierwszą osobą, która podzieli się swoją opinią – Twoje zdanie się liczy!
                </div>
                <Button type="secondary" @click="openAddReviewModal">Dodaj pierwszą opinię</Button>
            </div>
        </div>

        <div v-if="reviewsData.data.length"
             class="mb-10 flex flex-col sm:flex-row items-end justify-between gap-10 sm:gap-2">
            <div class="w-full sm:w-1/3 order-2 sm:order-1">
                <div class="border border-light rounded text-center py-4 px-6 sm:max-w-xs">
                    <div v-if="!reviewSubmitted">
                        <div class="font-semibold text-lg">
                            Masz ten produkt?
                        </div>
                        <div class="mt-2 mb-4 color-neutral-600">
                            Podziel się swoją opinią i pomóż innym dokonać wyboru!
                        </div>
                        <Button type="secondary" @click="openAddReviewModal">Dodaj opinię</Button>
                    </div>
                    <div v-else>
                        <div class="font-semibold text-lg">
                            Dziękujemy za opinię!
                        </div>
                        <div class="my-2 color-neutral-600">
                            Twoja ocena pomoże innym dokonać wyboru!
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full sm:w-1/3 order-1 sm:order-2">
                <div class="text-xl text-center mb-4">
                    {{ product.name }}
                </div>
                <div class="flex flex-col gap-4 items-center justify-center">
                    <div>
                        <span class="text-5xl text-amber-500 font-bold">{{
                                product.rating.toString().replace('.', ',')
                            }}</span>
                        <span class="text-3xl text-gray-300">/ 5</span>
                    </div>
                    <RatingDisplay :rating="product.rating" size="xl"/>
                </div>
            </div>
            <div class="w-full sm:w-1/3 order-3">
                <div v-if="reviewsData.data.length">
                    <div class="flex justify-end">
                        <div class="w-full sm:max-w-xs">
                            <FormField label="Sortuj opinie">
                                <select v-model="sort" @change="onSortChange">
                                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </FormField>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="reviews-list">
            <ReviewItem v-for="review in reviewsData.data"
                        :key="review.id"
                        :review="review"
                        @onEdit="openEditReviewModal"
            />
        </div>
    </div>

    <Button
        v-if="reviewsData.meta.current_page < reviewsData.meta.last_page"
        @click="loadMoreReviews"
        :disabled="isLoading"
        :loading="isLoading"
        type="ghost"
        full-width
        class="border"
    >
        Pokaż więcej opinii
    </Button>

    <ReviewModal
        @onClose="closeReviewModal"
        :product="product"
        :review="reviewToEdit"
        :show="showReviewModal"/>
</template>