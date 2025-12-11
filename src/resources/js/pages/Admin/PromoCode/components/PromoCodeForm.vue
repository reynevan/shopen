<script setup>

import AttributeConditionsSelect from "@shopen/pages/Admin/PromoCode/components/AttributeConditionsSelect.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import DateInput from "@shopen/components/admin/form/input/DateInput.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import {useForm} from "@inertiajs/vue3";
import CategoryMultiselect from "@shopen/components/admin/form/input/Category/CategoryMultiselect/CategoryMultiselect.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";

const props = defineProps(['promoCode', 'attributes', 'categories']);

const form = useForm({
    name: props.promoCode.name,
    codes: props.promoCode.codes ?? [{code: ''}],
    description: props.promoCode.description,
    is_active: props.promoCode.is_active,
    discount_type: props.promoCode.discount_type,
    discount_type_label: props.promoCode.discount_type_label,
    discount_value: props.promoCode.discount_value,
    max_discount_amount: props.promoCode.max_discount_amount,
    minimum_order_value: props.promoCode.minimum_order_value,
    applies_to: props.promoCode.applies_to,
    applies_to_label: props.promoCode.applies_to_label,
    applies_to_discounted: props.promoCode.applies_to_discounted,
    for_logged_users_only: props.promoCode.for_logged_users_only,
    usage_limit: props.promoCode.usage_limit,
    valid_from: props.promoCode.valid_from,
    valid_to: props.promoCode.valid_to,
    attributes: props.promoCode.attributes ?? [],
    categories: props.promoCode.categories ?? []
})

const save = () => {
    if (props.promoCode.id) {
        form.put(route('admin.promo-codes.update', props.promoCode.id), {
            preserveState: true,
            preserveScroll: true
        });
    } else {
        form.post(route('admin.promo-codes.store', props.promoCode.id), {
            preserveState: true,
            preserveScroll: true
        });
    }
}

const removeCode = (i) => {
    form.codes.splice(i, 1);
}

const addCode = () => {
    form.codes.push({code: null})
}

const discountTypeOptions = [
    {id: 'percentage', value: 'Procentowa'},
    {id: 'fixed_amount', value: 'Stała kwota'},
]
const appliesToOptions = [
    {id: 'cart', value: 'Cały koszyk'},
    {id: 'per_item', value: 'Każdy produkt osobno'},
]
</script>

<template>
    <ActionsPanel back-route="admin.promo-codes.index">
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <section>
        <FormField label="Aktywny">
            <Toggle v-model="form.is_active"/>
        </FormField>

        <FormField label="Nazwa" required>
            <Input v-model="form.name" required/>
        </FormField>

        <FormField label="Opis">
            <Input v-model="form.description"/>
        </FormField>

        <FormField label="Typ zniżki" required>
            <Select id="discount_type" v-model="form.discount_type" :options="discountTypeOptions"/>
        </FormField>

        <FormField label="Wartość zniżki" required>
            <Input type="number" v-model="form.discount_value" required/>
        </FormField>

        <FormField label="Zakres zastosowania" required>
            <Select id="applies_to" v-model="form.applies_to" :options="appliesToOptions"/>
        </FormField>

        <FormField label="Kategorie" v-if="form.applies_to === 'per_item'">
            <CategoryMultiselect :categories="categories" v-model="form.categories"/>
        </FormField>

        <FormField label="Atrybuty" v-if="form.applies_to === 'per_item'">
            <div>
                <AttributeConditionsSelect :attributes="attributes" :conditions="form.attributes"/>
            </div>
        </FormField>

        <FormField label="Maksymalna wartość zniżki" v-if="form.discount_type === 'percentage'">
            <template #description v-if="form.applies_to === 'cart'">
                Maksymalna wartość zniżki dla całego koszyka
            </template>
            <template #description v-if="form.applies_to === 'per_item'">
                Maksymalna wartość zniżki dla każdego produktu
            </template>
            <template #default>
                <Input type="number" v-model="form.max_discount_amount"/>
            </template>
        </FormField>

        <FormField label="Minimalna wartość zamówienia">
            <Input type="number" v-model="form.minimum_order_value"/>
        </FormField>

        <FormField label="Tylko dla zalogowanych">
            <Toggle v-model="form.for_logged_users_only"/>
        </FormField>

        <FormField label="Zastosuj dla przecenionych produktów" v-if="form.applies_to === 'per_item'">
            <Toggle v-model="form.applies_to_discounted"/>
        </FormField>

        <FormField label="Limit użyć">
            <Input type="number" v-model="form.usage_limit"/>
        </FormField>

        <FormField label="Aktywny od">
            <DateInput v-model="form.valid_from"/>
        </FormField>

        <FormField label="Aktywny do">
            <DateInput v-model="form.valid_to"/>
        </FormField>

        <FormField label="Kody" required>
            <div class="max-h-[500px] overflow-y-auto mb-4">
                <div class="space-y-2">
                    <table class="w-full max-w-[500px]">
                        <thead>
                            <tr>
                                <th class="w-full pr-2 py-1 font-light text-xs uppercase">Kod</th>
                                <th class="w-16 px-2 py-1 font-light text-xs uppercase">Ilość użyć</th>
                                <th class="w-12 px-2 py-1 font-light text-xs uppercase">Usuń</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(code, i) in form.codes">
                                <td class="pr-2">
                                    <Input v-model="code.code" required/>
                                </td>
                                <td class="px-2 text-center">
                                    {{ code.usage_count }}
                                </td>
                                <td class="px-2">
                                    <ActionButton type="cancel" @click="removeCode(i)"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <ActionButton type="add" @click="addCode">Dodaj kod</ActionButton>
        </FormField>
    </section>
</template>