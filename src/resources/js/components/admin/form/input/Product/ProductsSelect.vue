<script setup>
import {ref, onMounted} from "vue";
import BaseModal from "@shopen/components/admin/ui/BaseModal.vue";
import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import APIPagination from "../../../../frontend/ui/APIPagination.vue";
import Button from "../../../ui/Button.vue";
import ActionButton from "../../../ui/ActionButton.vue";
import Loader from "../../../ui/Loader.vue";

const model = defineModel({type: Array, default: () => []});

const emits = defineEmits(['update:selectedProducts']);

const props = defineProps({
    selectedProducts: {
        type: Array,
        default: () => []
    }
});

const products = ref(null);
const loading = ref(false);
const showModal = ref(false);

const modalSelection = ref([]);

const modalSelectedProducts = ref([]);

const openModal = async () => {
    loadProducts()

    modalSelection.value = [...(model.value || [])];

    modalSelectedProducts.value = [...(props.selectedProducts || [])];
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};


const toggleModalSelectedProduct = (product, checked) => {
    const index = modalSelectedProducts.value.findIndex(p => p.id === product.id);
    if (checked) {
        if (index === -1) {
            modalSelectedProducts.value.push(product);
        }
    } else {
        if (index !== -1) {
            modalSelectedProducts.value.splice(index, 1);
        }
    }
};

const removeProduct = (productId) => {
    model.value = model.value.filter(id => id !== productId);
    modalSelectedProducts.value = modalSelectedProducts.value.filter(product => product.id !== productId);
    const productsAfterRemove = props.selectedProducts.filter(product => product.id !== productId);
    emits('update:selectedProducts', [...productsAfterRemove]);
};

const confirmSelection = () => {
    model.value = [...modalSelection.value];

    const selectedIds = new Set(modalSelection.value);
    const byId = new Map();

    modalSelectedProducts.value.forEach(p => {
        if (selectedIds.has(p.id)) byId.set(p.id, p);
    });

    if (products.value?.data) {
        products.value.data.forEach(p => {
            if (selectedIds.has(p.id)) byId.set(p.id, p);
        });
    }

    emits('update:selectedProducts', Array.from(byId.values()));
    closeModal();
};

const sort = ref(null);
const dir = ref(null);
const q = ref(null);
const search = ref('');

const loadProducts = async (page = 1) => {
    loading.value = true;
    axios.get(route('admin.api.products.index'), {
        params: {
            sort: sort.value,
            dir: dir.value,
            q: q.value,
            page: page
        }
    }).then(response => {
        products.value = response.data;
    }).finally(() => {
        loading.value = false;
    });
};

const onSort = (field, direction) => {
    sort.value = field;
    dir.value = direction;
    loadProducts();
};

const onSearch = () => {
    q.value = search.value;
    loadProducts(1);
};

const onPaginate = (page) => {
    loadProducts(page);
};

</script>

<template>
    <div>
        <div v-if="selectedProducts && selectedProducts.length > 0"
             class="selected-products-list border rounded p-2 mb-4">
            <h4 class="font-semibold mb-2">Wybrane produkty:</h4>
            <table class="w-full table-primary">
                <thead class="bg-neutral-700 text-neutral-200 py-2">
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-center">Zdjęcie</th>
                    <th class="text-left">Nazwa</th>
                    <th class="text-left">SKU</th>
                    <th class="text-right">Status</th>
                    <th class="text-right">Cena</th>
                    <th class="text-right">Akcje</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="product in selectedProducts" :key="product.id">
                    <td class="text-left">{{ product.id }}</td>
                    <td>
                        <div class="flex justify-center">
                            <img :src="product.image" width="40" v-if="product.image">
                        </div>
                    </td>
                    <td class="text-left">{{ product.attributes.name }}</td>
                    <td class="text-left">{{ product.sku }}</td>
                    <td class="text-right">
                        <span v-if="product.attributes.is_active">Aktywny</span>
                        <span v-else>Nieaktywny</span>
                    </td>
                    <td class="text-right">{{ product.price.final_price ?? '-' }}</td>
                    <td class="text-right">
                        <ActionButton type="remove" @click="removeProduct(product.id)"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <Button @click="openModal">
            <span v-if="selectedProducts && selectedProducts.length > 0">Edytuj produkty</span>
            <span v-else>Wybierz produkty</span>
        </Button>

        <BaseModal :show="showModal" @onClose="closeModal" full-width>
            <template #header>
                <span v-if="selectedProducts && selectedProducts.length > 0">Edytuj produkty</span>
                <span v-else>Wybierz produkty</span>
            </template>
            <template #default>
                <div v-if="loading && !products?.data?.length" class="flex justify-center items-center py-10">
                    <Loader :loading="true"/>
                </div>
                <div v-else>
                    <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
                        <div class="w-full max-w-md">
                            <form @submit.prevent="onSearch" class="flex items-center">
                                <Input v-model="search" id="search" placeholder="Szukaj po nazwie, SKU..."
                                       class="mr-2"/>
                                <Button type="submit">Szukaj</Button>
                            </form>
                        </div>
                        <div class="w-full sm:w-auto flex justify-end">
                            <APIPagination v-if="products?.meta" :meta="products.meta" @onPaginate="onPaginate" :loading="loading" />
                        </div>
                    </div>

                    <DataTable
                        v-if="products"
                        :loading="loading"
                        table-class="w-full"
                        head-class="bg-neutral-700 text-neutral-200 py-2"
                        td-class="py-2"
                        @onSort="onSort"
                        :default-sort="[sort, dir]"
                        :data="products.data"
                        :meta="products.meta"
                        top="top-0"
                    >
                        <TableColumn label="Wybierz" v-slot="data" width="75px">
                            <input
                                type="checkbox"
                                :value="data.row.id"
                                v-model="modalSelection"
                                @change="toggleModalSelectedProduct(data.row, $event.target.checked)"
                                class="w-5 h-5">
                        </TableColumn>

                        <TableColumn field="id" label="ID" sortable v-slot="data" width="35px">
                            {{ data.row.id }}
                        </TableColumn>

                        <TableColumn label="Zdjęcie" v-slot="data" width="70px">
                            <img :src="data.row.image"
                                 width="50px"
                                 class="border"
                                 v-if="data.row.image">
                        </TableColumn>

                        <TableColumn field="sku" label="SKU" v-slot="data">
                            {{ data.row.sku }}
                        </TableColumn>

                        <TableColumn field="is_active" label="Status" sortable v-slot="data">
                            <span v-if="data.row.attributes.is_active" class="text-green-600">Aktywny</span>
                            <span v-else class="text-red-600">Nieaktywny</span>
                        </TableColumn>

                        <TableColumn field="name" label="Nazwa" sortable v-slot="data">
                            {{ data.row.attributes.name }}
                        </TableColumn>

                        <TableColumn field="price" label="Bazowa Cena" sortable v-slot="data" width="135px">
                            {{ data.row.price ? data.row.price.price : '' }}
                        </TableColumn>

                        <TableColumn field="final_price" label="Cena" sortable v-slot="data" width="135px">
                            {{ data.row.price ? data.row.price.final_price : '' }}
                        </TableColumn>
                    </DataTable>
                    <div class="flex justify-end mt-4">
                        <APIPagination v-if="products?.meta" :meta="products.meta" @onPaginate="onPaginate"/>
                    </div>
                </div>
            </template>
            <template #buttons>
                <div class="flex justify-end w-full gap-6">
                    <Button type="neutral" @click="closeModal">Anuluj</Button>
                    <Button type="success" @click="confirmSelection">Wybierz</Button>
                </div>
            </template>
        </BaseModal>
    </div>
</template>