<script setup>
import { ref, onMounted } from "vue";
import BaseModal from "@shopen/components/admin/ui/BaseModal.vue";
import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import APIPagination from "../../../../frontend/ui/APIPagination.vue";
import Button from "../../../ui/Button.vue";

// v-model będzie zawierał listę ID wybranych produktów, np. [1, 5, 12]
const model = defineModel({ type: Array, default: () => [] });

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

// Tymczasowa lista ID produktów wybranych w modalu
const modalSelection = ref([]);
const modalSelectedProducts = ref([]);

const openModal = () => {
    // Kiedy otwieramy modal, inicjujemy tymczasowy wybór aktualnie wybranymi produktami
    // Używamy spread operator `[...]` aby stworzyć kopię, a nie referencję
    modalSelection.value = [...(model.value || [])];
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const toggleModalSelectedProduct = (product) => {
    const index = modalSelectedProducts.value.findIndex(p => p.id === product.id);
    if (index !== -1) {
        // Produkt jest w tablicy, usuń go
        modalSelectedProducts.value.splice(index, 1);
    } else {
        // Produktu nie ma w tablicy, dodaj go
        modalSelectedProducts.value.push(product);
    }
};

// Funkcja do usuwania produktu z listy wybranych
const removeProduct = (productId) => {
    model.value = model.value.filter(id => id !== productId);
    modalSelectedProducts.value = modalSelectedProducts.value.filter(product => product.id !== productId);
    const products = props.selectedProducts.filter(product => product.id !== productId);
    emits('update:selectedProducts', [...products]);
}

// Funkcja zatwierdzająca wybór z modala
const confirmSelection = () => {
    // Aktualizujemy główny model wartościami z modala
    model.value = modalSelection.value;
    emits('update:selectedProducts', [...modalSelectedProducts.value]);
    closeModal();
}

// --- Logika ładowania, sortowania i wyszukiwania produktów w modalu ---
const sort = ref(null);
const dir = ref(null);
const q = ref(null);
const search = ref(''); // Osobna referencja dla inputu wyszukiwania

const loadProducts = (page = 1) => {
    loading.value = true;
    axios.get(route('admin.api.products.index'), {
        params: {
            sort: sort.value,
            dir: dir.value,
            q: q.value,
            page: page
        }
    })
        .then(response => {
            products.value = response.data;
        })
        .finally(() => {
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
    loadProducts(1); // Resetuj do pierwszej strony po wyszukaniu
};

const onPaginate = (page) => {
    loadProducts(page);
};

onMounted(() => {
    loadProducts();
});
</script>

<template>
    <div>
        <!-- Lista już wybranych produktów -->
        <div v-if="selectedProducts && selectedProducts.length > 0" class="selected-products-list border rounded p-2 mb-4">
            <h4 class="font-semibold mb-2">Wybrane produkty:</h4>
            <table class="w-full">
                <thead class="bg-neutral-700 text-neutral-200 py-2">
                <tr>
                    <th>ID</th>
                    <th>Zdjęcie</th>
                    <th>SKU</th>
                    <th>Status</th>
                    <th>Nazwa</th>
                    <th>Cena</th>
                    <th>Akcje</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="product in selectedProducts" :key="product.id">
                    <td class="py-2">{{ product.id }}</td>
                    <td class="py-2"><img :src="product.image" width="40"  v-if="product.image"></td>
                    <td class="py-2">{{ product.sku }}</td>
                    <td class="py-2">
                        <span v-if="product.attributes.is_active">Aktywny</span>
                        <span v-else>Nieaktywny</span>
                    </td>
                    <td class="py-2">{{ product.attributes.name }}</td>
                    <td class="py-2">{{ product.price.final_price ?? '-' }}</td>
                    <td class="py-2"><Button type="danger" @click="removeProduct(product.id)" size="sm">Usuń</Button></td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Przycisk otwierający modal -->
        <Button @click="openModal">Wybierz produkty</Button>

        <BaseModal :show="showModal" @onClose="closeModal" full-width>
            <template #header>
                Wybierz produkty
            </template>
            <template #default>
                <div class="flex flex-col sm:flex-row justify-between mb-4 gap-4">
                    <div class="w-full max-w-md">
                        <form @submit.prevent="onSearch" class="flex items-center">
                            <Input v-model="search" id="search" placeholder="Szukaj po nazwie, SKU..." class="mr-2"/>
                            <Button type="submit">Szukaj</Button>
                        </form>
                    </div>
                    <div class="w-full sm:w-auto flex justify-end">
                        <APIPagination v-if="products?.meta" :meta="products.meta" @onPaginate="onPaginate"/>
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
                >
                    <TableColumn label="Wybierz" v-slot="data" width="75px">
                        <!-- Powiązanie checkboxa z tymczasową listą wybranych ID -->
                        <input type="checkbox"
                               :value="data.row.id"
                               v-model="modalSelection"
                               @change="toggleModalSelectedProduct(data.row)"
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
            </template>
            <template #buttons>
                <div class="flex justify-end w-full gap-6">
                    <Button type="neutral" @click="closeModal">Anuluj</Button>
                    <!-- Przycisk zatwierdzający wybór -->
                    <Button type="success" @click="confirmSelection">Wybierz</Button>
                </div>
            </template>
        </BaseModal>
    </div>
</template>