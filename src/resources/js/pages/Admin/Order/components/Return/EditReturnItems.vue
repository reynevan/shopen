<script setup>

import {useForm} from "@inertiajs/vue3";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";

const props = defineProps(['items']);

const emits = defineEmits(['onItemsUpdate'])

const form = useForm({
    items: props.items.map(item => {
        item.quantity_to_return = item.available_to_return_quantity;
        return item;
    }),
})
const onInput = () => {
    emits('onItemsUpdate', form.items);
}
</script>

<template>
    <table class="w-full">
        <thead class="sticky top-0 bg-panel py-2">
        <tr>
            <th class="uppercase text-sm font-normal text-left pr-4 py-2 ">Produkt</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Zamówiona ilość</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Ilość do zwrotu</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Aktualizuj magazyn</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Jednostka miary</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Cena jedn. netto</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Rabat netto</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Wartość netto</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Wartość brutto</th>
        </tr>
        <tr>
            <th class="h-[1px] bg-border-primary" colspan="10"></th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b hover:bg-accent/50 transition-colors" v-for="(item, i) in form.items">
            <td class="pr-4 py-2">
                <div class="flex items-start pl-4 gap-4">
                    <div>
                        <div>{{ item.name }}</div>
                        <div v-if="item.sku" class="mt-1 text-neutral-500 text-sm">SKU: {{ item.sku }}</div>
                        <div v-if="item.product" class="text-neutral-500 text-sm"
                             v-for="attribute in item.product.variant_attributes">
                            {{ attribute.name }}: {{ attribute.value }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-2 text-right">
                <div v-if="item.returned_quantity > 0">
                    <div class="whitespace-nowrap">
                        <span class="text-xs uppercase">Zamówione:</span> {{ item.quantity }}
                    </div>
                    <div class="whitespace-nowrap">
                        <span class="text-xs uppercase">Zwrócone:</span> {{ item.returned_quantity }}
                    </div>
                </div>
                <div v-else>
                    {{ item.quantity }}
                </div>
            </td>
            <td class="px-4 py-2 text-right">
                <Input class="text-right"
                       @input="onInput"
                       :id="`quantity-${i}`"
                       :max="item.available_to_return_quantity"
                       v-model="item.quantity_to_return"
                       type="number"/>
            </td>
            <td class="px-4 py-2 text-right">
                <Toggle :id="`restock-${i}`" v-model="item.restock"/>
            </td>
            <td class="px-4 py-2 text-right">
                {{ item.unit }}
            </td>
            <td class="px-4 py-2 text-right">
                {{ $currency(item.price_net) }}

            </td>
            <td class="px-4 py-2 text-right">
                {{ $currency(item.discount_amount_net) }}
            </td>

            <td class="px-4 py-2 text-right">
                {{ $currency(item.total_net) }}
            </td>

            <td class="px-4 py-2 text-right">
                {{ $currency(item.total) }}
            </td>
        </tr>
        </tbody>
    </table>
</template>