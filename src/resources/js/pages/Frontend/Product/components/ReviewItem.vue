<script setup>
import {router, usePage} from '@inertiajs/vue3';
import {useAuthStore} from "@shopen/stores/auth.js";
import RatingDisplay from "@shopen/components/frontend/product/RatingDisplay.vue";
import IconThumbUp from "@shopen/components/icons/IconThumbUp.vue";
import IconThumbDown from "@shopen/components/icons/IconThumbDown.vue";
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconEdit from "@shopen/components/icons/IconEdit.vue";
import IconTrash from "@shopen/components/icons/IconTrash.vue";
import axios from "axios";
import {computed, ref} from "vue";
import IconLoader from "../../../../components/icons/IconLoader.vue";

const props = defineProps({
    review: Object,
});

const emits = defineEmits(['onEdit', 'onDelete'])
const auth = useAuthStore();
const page = usePage();

const loading = ref(false);

const editable = computed(() => auth.user && props.review.user.id === auth.user.id);

const edit = () => {
    emits('onEdit', props.review);
}

const remove = () => {
    if (!window.confirm('Na pewno chcesz usunąć tę opinię?')) {
        return;
    }
    router.delete(route('products.reviews.delete', props.review.id), {
        preserveScroll: true,
        only: ['errors', 'flash', 'product', 'reviews', 'reviewSubmitted']
    })
}

const submitVote = (voteValue) => {

    loading.value = true;
    axios.post(route('api.products.reviews.vote', props.review.id), {
        vote: voteValue
    }).then(response => {
        props.review.helpful_votes_count = response.data.helpful_votes_count;
        props.review.unhelpful_votes_count = response.data.unhelpful_votes_count;
    }).catch(error => {
        page.props.flash = error.response.data;
    }).finally(() => {
        loading.value = false;
    })
};
</script>

<template>
    <div class="review-item my-2 py-2 border-t">
        <!-- Nagłówek z informacjami o autorze i przyciskami akcji -->
        <div class="flex justify-between items-start mb-2">
            <div class="author-info">
                <div class="author-name font-semibold">{{ review.user.first_name }}</div>
                <div v-if="review.is_verified_purchase" class="verified-badge flex items-center gap-2 mt-1">
                    <span class="text-green-600"><IconCheckCircle/></span>
                    <span class="text-neutral-600 text-sm">Zweryfikowany zakup</span>
                </div>
            </div>

            <!-- Przyciski Edytuj i Usuń -->
            <div v-if="editable" class="flex items-center gap-2">
                <button
                    @click.prevent="edit"
                    class="px-3 py-1 cursor-pointer flex items-center gap-2 text-sm text-link-600 hover:text-link-800 hover:bg-link-50 rounded transition-colors duration-200"
                >
                    <IconEdit/>
                    Edytuj
                </button>
                <button
                    @click.prevent="remove"
                    class="px-3 py-1 cursor-pointer flex items-center gap-2 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors duration-200"
                >
                    <IconTrash/>
                    Usuń
                </button>
            </div>
        </div>

        <!-- Ocena i data -->
        <div class="flex items-center mb-2">
            <RatingDisplay :rating="review.rating" :max-stars="5"/>
            <div class="ml-2 text-sm text-gray-500">{{ review.created_at }}</div>
        </div>

        <!-- Treść opinii -->
        <p class="comment whitespace-pre-wrap mb-3 text-gray-700">{{ review.comment }}</p>

        <!-- Przyciski głosowania -->
        <div class="review-actions flex gap-2 mb-3">
            <button
                :disabled="loading || !auth.isLoggedIn"
                class="cursor-pointer flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-100 text-neutral-500 hover:text-neutral-600 transition-all duration-200 rounded"
                @click="submitVote(1)">
                <IconThumbUp class="w-4 h-4"/>
                <span v-if="!loading">{{ review.helpful_votes_count }}</span>
                <IconLoader v-if="loading"/>
            </button>
            <button
                :disabled="loading || !auth.isLoggedIn"
                class="cursor-pointer flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-100 text-neutral-500 hover:text-neutral-600 transition-all duration-200 rounded"
                @click="submitVote(-1)">
                <IconThumbDown class="w-4 h-4"/>
                <span v-if="!loading">{{ review.unhelpful_votes_count }}</span>
                <IconLoader v-if="loading"/>
            </button>
        </div>

        <!-- Odpowiedź od sklepu -->
        <div v-if="review.reply" class="admin-reply bg-gray-50 p-3 rounded mt-3">
            <strong class="text-sm">Odpowiedź od sklepu ({{ review.reply.user.first_name }}):</strong>
            <p class="mt-1 text-sm text-gray-700">{{ review.reply.reply_text }}</p>
        </div>
    </div>
</template>