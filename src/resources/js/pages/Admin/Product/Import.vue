<script setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import {computed, ref} from "vue";
import FormField from "../../../components/admin/form/FormField.vue";
import Input from "../../../components/admin/form/input/Input.vue";

defineOptions({layout: AdminLayout})

const page = usePage();

const form = useForm({
    file: null
})

// Computed properties dla danych z sesji
const validationStatus = computed(() => page.props.flash?.validation_status);
const validationMessage = computed(() => page.props.flash?.validation_message);
const validationSummary = computed(() => page.props.flash?.validation_summary);
const validationErrors = computed(() => page.props.flash?.validation_errors || []);
const validationWarnings = computed(() => page.props.flash?.validation_warnings || []);
const isValidated = computed(() => page.props.flash?.is_validated || false);
const isValid = computed(() => validationStatus.value === 'success');

const validate = () => {
    form.post(route('admin.products.import.validate'), {
        preserveScroll: true
    });
}

const onFileSelect = (event) => {
    form.file = event.target.files[0];
}

const importProducts = () => {
    form.post(route('admin.products.import.process'), {
        preserveScroll: true
    });
}
</script>

<template>
    <Head title="Import produktów"/>
    <ActionsPanel back-route="admin.products.index">
        <template #title>
            <PageTitle>Produkty - Import</PageTitle>
        </template>
        <Button
            v-if="!isValidated || !isValid"
            @click="validate"
            :disabled="!form.file || form.processing"
            :loading="form.processing"
        >
            Sprawdź plik
        </Button>
        <Button
            v-if="isValidated && isValid"
            @click="importProducts"
            variant="success"
        >
            Importuj
        </Button>
    </ActionsPanel>

    <div class="space-y-6">
        <!-- Formularz wyboru pliku -->
        <div class="bg-white p-6 rounded-lg shadow">
            <FormField label="Plik .csv" :error="form.errors.file">
                <Input
                    @input="onFileSelect"
                    type="file"
                    id="import_file"
                    accept=".csv,.txt"
                    :class="{'border-red-500': form.errors.file}"
                />
            </FormField>
        </div>

        <!-- Status walidacji -->
        <div v-if="validationStatus" class="bg-white p-6 rounded-lg shadow">
            <div
                :class="{
                    'bg-green-50 border-green-200 text-green-800': validationStatus === 'success',
                    'bg-red-50 border-red-200 text-red-800': validationStatus === 'error'
                }"
                class="border rounded-lg p-4"
            >
                <div class="flex items-center">
                    <div
                        :class="{
                            'text-green-500': validationStatus === 'success',
                            'text-red-500': validationStatus === 'error'
                        }"
                        class="mr-3"
                    >
                        <svg v-if="validationStatus === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium">{{ validationMessage }}</h3>
                </div>
            </div>
        </div>

        <!-- Podsumowanie walidacji -->
        <div v-if="validationSummary" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Podsumowanie walidacji</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ validationSummary.total_rows }}</div>
                    <div class="text-sm text-gray-500">Wszystkich wierszy</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ validationSummary.valid_rows }}</div>
                    <div class="text-sm text-gray-500">Poprawnych</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ validationSummary.invalid_rows }}</div>
                    <div class="text-sm text-gray-500">Niepoprawnych</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ validationSummary.missing_headers?.length || 0 }}</div>
                    <div class="text-sm text-gray-500">Brakujących nagłówków</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ validationSummary.duplicate_skus?.length || 0 }}</div>
                    <div class="text-sm text-gray-500">Duplikatów SKU</div>
                </div>
            </div>
        </div>

        <!-- Brakujące nagłówki -->
        <div v-if="validationSummary?.missing_headers?.length" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-orange-600">Brakujące nagłówki</h3>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="header in validationSummary.missing_headers"
                    :key="header"
                    class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm"
                >
                    {{ header }}
                </span>
            </div>
        </div>

        <!-- Duplikaty SKU -->
        <div v-if="validationSummary?.duplicate_skus?.length" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-purple-600">Duplikaty SKU</h3>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="sku in validationSummary.duplicate_skus"
                    :key="sku"
                    class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm"
                >
                    {{ sku }}
                </span>
            </div>
        </div>

        <!-- Błędy walidacji -->
        <div v-if="validationErrors.length" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-red-600">Błędy walidacji</h3>
            <div class="max-h-96 overflow-y-auto">
                <ul class="space-y-2">
                    <li
                        v-for="(error, index) in validationErrors"
                        :key="index"
                        class="p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm"
                    >
                        {{ error }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Ostrzeżenia -->
        <div v-if="validationWarnings.length" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-yellow-600">Ostrzeżenia</h3>
            <div class="max-h-96 overflow-y-auto">
                <ul class="space-y-2">
                    <li
                        v-for="(warning, index) in validationWarnings"
                        :key="index"
                        class="p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-800 text-sm"
                    >
                        {{ warning }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>