<script setup>

import {ref} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import ProductThumbnailImage from "@shopen/components/admin/product/ProductThumbnailImage.vue";
import ReviewDetailsModal from "./ReviewDetailsModal.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";

const props = defineProps({
    reviews: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;
const status = page.props.status;

const reviewToEdit = ref(null);
const showEditModal = ref(false);

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.products.reviews.index'), {
        sort: field,
        dir: dir,
        status: status
    }, {})
}

const clearSearch = () => {
    search.value = null;
    onSearch();
}

const onSearch = () => {
    router.get(route('admin.products.reviews.index'), {
        sort: sort,
        dir: dir,
        q: search.value,
        status: status
    }, {})
}

const filter = (status = null) => {
    const params = {
        sort: sort,
        dir: dir,
        q: search.value
    }
    if (status) {
        params.status = status;
    }
    router.get(route('admin.products.reviews.index'), params, {})
}

const accept = (review) => {
    router.put(route('admin.products.reviews.accept', review.id), {}, {
        preserveScroll: true,
        onFinish: () => {showEditModal.value = false; reviewToEdit.value = null;}
    })
}

const cancel = (review) => {
    router.put(route('admin.products.reviews.reject', review.id), {}, {
        preserveScroll: true,
        onFinish: () => {showEditModal.value = false; reviewToEdit.value = null;}
    })
}

const remove = (review) => {
    if (!window.confirm("Czy na pewno chcesz usunąć tę opinię? Tej akcji nie można cofnąć.")) {
        return
    }
    router.delete(route('admin.products.reviews.delete', review.id), {}, {
        preserveScroll: true,
        onFinish: () => {showEditModal.value = false; reviewToEdit.value = null;}
    })
}

const edit = (review) => {
    reviewToEdit.value = review;
    showEditModal.value = true;
}

const closeEditModal = () => {
    showEditModal.value = false;
}

</script>

<template>
    <div class="flex flex-col lg:flex-row gap-10 justify-between items-end mb-4">

        <div class="flex items-center gap-2 my-2">
            <Button @click="filter()">Wszystkie</Button>
            <Button type="warning" @click="filter('pending')">
                <i class="bi bi-hourglass-split mr-2"></i> Oczekujące
            </Button>
            <Button type="danger" @click="filter('rejected')">
                <i class="bi bi-x-lg mr-2"></i> Odrzucone
            </Button>
            <Button type="success" @click="filter('approved')">
                <i class="bi bi-check2 mr-2"></i> Zaakceptowane
            </Button>
        </div>

        <form @submit.prevent="onSearch" class="w-full max-w-md">
            <div class="flex justify-end">
                <button class="mr-2 cursor-pointer" @click="clearSearch" v-show="search">
                    <i class="bi bi-x-lg"></i>
                </button>

                <Input v-model="search" id="search" class="mr-2 max-w-md"/>

                <Button type="neutral" submit>
                    <i class="bi bi-search mr-2"></i> Szukaj
                </Button>
            </div>
        </form>
    </div>

    <DataTable
        table-class="w-full"
        head-class="bg-neutral-700 text-neutral-200 py-2"
        td-class="py-2"
        @onSort="onSort"
        :default-sort="[sort, dir]"
        :data="reviews.data"
        paginated
        :meta="reviews.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn field="product" label="Produkt" sortable v-slot="data" width="400px">
            <div class="flex items-center">
                <ProductThumbnailImage :product="data.row.product" size="sm"/>
                <div class="ml-2">
                    <Link :href="data.row.product.url">{{ data.row.product.name }}</Link>
                </div>
            </div>
        </TableColumn>

        <TableColumn field="rating" label="Ocena" sortable v-slot="data" width="100px">
            <div class="flex items-center gap-1 justify-center">
                <div>
                    {{ data.row.rating }} <i class="bi bi-star-fill"></i>
                </div>
                <div v-if="data.row.rating_to_verify && data.row.rating_to_verify !== data.row.rating">
                    <i class="bi bi-arrow-right-short"></i>
                </div>
                <div v-if="data.row.rating_to_verify">
                    {{ data.row.rating_to_verify }} <i class="bi bi-star-fill"></i>
                </div>
            </div>
        </TableColumn>

        <TableColumn field="created_at" label="Data" sortable v-slot="data" width="200px">
            {{ data.row.created_at }}
        </TableColumn>

        <TableColumn field="comment" label="Komentarz" sortable v-slot="data">
            <div class="mb-1">
                {{ data.row.comment_to_verify ?? data.row.comment }}
            </div>
            <button class="text-sm text-link hover:text-link-hover transition-colors"
                    @click="edit(data.row)"
                    v-if="data.row.comment_to_verify">
                Pokaż oryginał
            </button>
        </TableColumn>

        <TableColumn field="status" label="Status" v-slot="data" sortable>
            <div class="text-center">
                <div class="inline-block px-2 py-1 rounded" :class="{
                    'bg-green-100 text-green-700': data.row.status === 'approved',
                    'bg-yellow-100 text-yellow-700': data.row.status === 'pending',
                    'bg-blue-100 text-blue-700': data.row.status === 'pending_edit',
                    'bg-red-100 text-red-700': data.row.status === 'rejected',
                }">
                    <i v-if="data.row.status === 'pending'" class="bi bi-hourglass-split"></i>
                    <i v-if="data.row.status === 'approved'" class="bi bi-check2"></i>
                    <i v-if="data.row.status === 'pending_edit'" class="bi bi-pencil-square"></i>
                    <i v-if="data.row.status === 'rejected'" class="bi bi-x-lg"></i>
                    {{ data.row.status_label }}
                </div>
            </div>
        </TableColumn>

        <TableColumn label="Akcje" v-slot="data">
            <div class="flex items-center gap-2 justify-end">
                <ActionButton type="view" @click="edit(data.row)" title="Podgląd"/>
                <ActionButton v-if="data.row.status !== 'approved'" type="accept" @click="accept(data.row)" title="Zaakceptuj"/>
                <ActionButton v-if="data.row.status !== 'rejected'" type="cancel" @click="cancel(data.row)" title="Odrzuć"/>
                <ActionButton type="remove" @click="remove(data.row)" title="Usuń"/>
            </div>
        </TableColumn>
    </DataTable>

    <ReviewDetailsModal :review="reviewToEdit"
                        @onAccept="accept"
                        @onCancel="cancel"
                        @onRemove="remove"
                        @onClose="closeEditModal"
                        :show="showEditModal"/>

</template>