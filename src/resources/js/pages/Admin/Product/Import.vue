<script setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import {computed, ref} from "vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";

defineOptions({layout: AdminLayout})

const page = usePage();

const form = useForm({
    file: null
})

const importSummary = computed(() => page.props.import_summary);
const validationStatus = computed(() => page.props.validation_status);
const validationMessage = computed(() => page.props.validation_message);
const validationSummary = computed(() => page.props.validation_summary);
const validationErrors = computed(() => page.props.validation_errors || []);
const validationWarnings = computed(() => page.props.validation_warnings || []);
const isValidated = ref(page.props.is_validated || false);
const isValid = computed(() => validationStatus.value === 'success');

const validate = () => {
    form.post(route('admin.products.import.validate'), {
        preserveScroll: true,
        onSuccess: () => {
            isValidated.value = true;
        }
    });
}

const onFileSelect = (event) => {
    form.file = event.target.files[0];
    isValidated.value = false;
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

            <FormField>
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
            </FormField>
        </div>

        <!-- Status walidacji -->
        <div v-if="validationStatus" class="bg-white p-6 shadow">
            <div
                :class="{
                    'border-green-200 text-green-800': validationStatus === 'success',
                    'border-red-200 text-red-800': validationStatus === 'error'
                }"
                class="border p-4"
            >
                <div class="flex items-center">
                    <div
                        :class="{
                            'text-green-500': validationStatus === 'success',
                            'text-red-500': validationStatus === 'error'
                        }"
                        class="mr-3"
                    >
                        <i v-if="validationStatus === 'success'" class="bi bi-check-circle"></i>
                        <i v-else class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-medium">{{ validationMessage }}</h3>
                </div>
            </div>
        </div>

        <!-- Podsumowanie importu -->
        <div v-if="importSummary" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Podsumowanie importu</h3>
            <div class="flex divide-x">
                <div class="text-center px-6">
                    <div class="text-2xl font-bold">{{ importSummary.total_rows }}</div>
                    <div class="text-xs uppercase text-gray-500">Wszystkich wierszy</div>
                </div>
                <div class="text-center px-6">
                    <div class="text-2xl font-bold">{{ importSummary.created }}</div>
                    <div class="text-xs uppercase text-gray-500">Utworzonych produktów</div>
                </div>
                <div class="text-center px-6">
                    <div class="text-2xl font-bold">{{ importSummary.updated }}</div>
                    <div class="text-xs uppercase text-gray-500">Zaktualizowanych produktów</div>
                </div>
                <div class="text-center px-6">
                    <div class="text-2xl font-bold">{{ importSummary.errors }}</div>
                    <div class="text-xs uppercase text-gray-500">Błędów</div>
                </div>
            </div>
        </div>

        <!-- Błędy importu -->
        <div v-if="importSummary?.error_messages?.length" class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4 text-red-600">Błędy importu</h3>
            <div class="max-h-96 overflow-y-auto">
                <ul class="space-y-2">
                    <li
                        v-for="(error, index) in importSummary.error_messages"
                        :key="index"
                        class="p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm"
                    >
                        {{ error }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Podsumowanie walidacji -->
        <div v-if="validationSummary" class="bg-white px-6 pb-6 pt-2 rounded shadow">
            <h3 class="text-lg font-medium mb-4">Podsumowanie walidacji</h3>
            <div class="flex divide-x">
                <div class="text-center px-4">
                    <div class="text-2xl font-bold">{{ validationSummary.total_rows }}</div>
                    <div class="text-xs uppercase text-gray-500">Wszystkich wierszy</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-2xl font-bold">{{ validationSummary.valid_rows }}</div>
                    <div class="text-xs uppercase text-gray-500">Poprawnych</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-2xl font-bold">{{ validationSummary.invalid_rows }}</div>
                    <div class="text-xs uppercase text-gray-500">Niepoprawnych</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-2xl font-bold">{{ validationSummary.missing_headers?.length || 0 }}</div>
                    <div class="text-xs uppercase text-gray-500">Brakujących nagłówków</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-2xl font-bold">{{ validationSummary.duplicate_skus?.length || 0 }}</div>
                    <div class="text-xs uppercase text-gray-500">Duplikatów SKU</div>
                </div>
            </div>
        </div>

        <!-- Brakujące nagłówki -->
        <div v-if="validationSummary?.missing_headers?.length" class="bg-white p-6  shadow">
            <h3 class="text-lg font-medium mb-4 text-orange-600">Brakujące nagłówki</h3>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="header in validationSummary.missing_headers"
                    :key="header"
                    class="px-3 py-1 border rounded-full text-sm"
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