<script setup>
import { computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";

const props = defineProps(["items"]);

const emits = defineEmits(["onItemsUpdate", "onEditingChange"]);

const form = useForm({
    items: (props.items ?? []).map((item) => ({
        ...item,
        editing: false,
    })),
});

const isAnyEditing = computed(() => form.items.some((item) => !!item.editing));

watch(
    isAnyEditing,
    (val) => {
        emits('onEditingChange', val);
    },
    { immediate: true }
);

const editItem = (item) => {
    item.editing = true;
};

const cancelEdit = (item) => {
    const original = props.items.find((_item) => _item.id === item.id);

    if (original) {
        Object.assign(item, JSON.parse(JSON.stringify(original)));
    }

    item.editing = false;
};

const saveItem = (item) => {
    axios.post(route("admin.api.invoices.items.recalculate"), { item }).then((response) => {
        Object.assign(item, response.data);
        item.editing = false;
        emits("onItemsUpdate", form.items);
    });
};

const removeItem = (i) => {
    form.items.splice(i, 1);
    emits("onItemsUpdate", form.items);
};
</script>

<template>
    <table class="w-full">
        <thead class="sticky top-0 bg-panel py-2">
        <tr>
            <th class="uppercase text-sm font-normal text-left pr-4 py-2">Produkt</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Ilość</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Jednostka miary</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Cena jedn. netto</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Rabat netto</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Stawka VAT</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Kwota VAT</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Wartość netto</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Wartość brutto</th>
            <th class="uppercase text-sm font-normal w-[100px] py-2 text-center">Akcje</th>
        </tr>
        <tr>
            <th class="h-[1px] bg-border-primary" colspan="10"></th>
        </tr>
        </thead>

        <tbody>
        <tr
            class="border-b hover:bg-accent/50 transition-colors"
            v-for="(item, i) in form.items"
            :key="item.id ?? i"
        >
            <td class="pr-4 py-2">
                <div class="flex items-start pl-4 gap-4">
                    <div>
                        <div>{{ item.name }}</div>
                        <div v-if="item.sku" class="mt-1 text-neutral-500 text-sm">SKU: {{ item.sku }}</div>
                        <div
                            v-if="item.product"
                            class="text-neutral-500 text-sm"
                            v-for="attribute in item.product.variant_attributes"
                            :key="attribute.name"
                        >
                            {{ attribute.name }}: {{ attribute.value }}
                        </div>
                    </div>
                </div>
            </td>

            <td class="px-4 py-2 text-right">
                <span v-if="!item.editing">{{ item.quantity }}</span>
                <Input v-else class="text-right" :id="`quantity-${i}`" v-model="item.quantity" type="number" />
            </td>

            <td class="px-4 py-2 text-right">
                <span v-if="!item.editing">{{ item.unit }}</span>
                <Input v-else class="text-right" :id="`unit-${i}`" v-model="item.unit" />
            </td>

            <td class="px-4 py-2 text-right">
                <span v-if="!item.editing">{{ $currency(item.price_net) }}</span>
                <Input v-else class="text-right" :id="`price_net-${i}`" v-model="item.price_net" />
            </td>

            <td class="px-4 py-2 text-right">
                <span v-if="!item.editing">{{ $currency(item.discount_amount_net) }}</span>
                <Input v-else class="text-right" :id="`discount_amount_net-${i}`" v-model="item.discount_amount_net" />
            </td>

            <td class="px-4 py-2 text-right">
                <span v-if="!item.editing">{{ item.tax_rate }}</span>
                <Input v-else class="text-right" :id="`tax_rate-${i}`" v-model="item.tax_rate" />
            </td>

            <td class="px-4 py-2 text-right">
                {{ $currency(item.tax_amount) }}
            </td>

            <td class="px-4 py-2 text-right">
                {{ $currency(item.total_net) }}
            </td>

            <td class="px-4 py-2 text-right">
                {{ $currency(item.total) }}
            </td>

            <td class="py-2 px-2 text-right">
                <div class="flex justify-end">
                    <ActionButtons>
                        <ActionButton v-if="!item.editing" type="edit" @click="editItem(item)" />
                        <ActionButton v-if="!item.editing" type="remove" @click="removeItem(i)" />
                        <ActionButton v-if="item.editing" type="cancel" @click="cancelEdit(item)" />
                        <ActionButton v-if="item.editing" type="accept" @click="saveItem(item)" />
                    </ActionButtons>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>