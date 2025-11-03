<script setup>
    defineProps(['product', 'attributes'])
</script>

<template>
    <div class="attributes">
        <div class="flex items-center gap-4 py-2">
            <div class="attribute-name w-1/3">SKU</div>
            <div class="attribute-value w-2/3">{{ product.sku }}</div>
        </div>
        <template v-for="attribute in attributes" :key="attribute.id">
            <div v-if="product.attributes[attribute.code] ?? false" class="flex items-center gap-4 py-2">
                <div class="attribute-name w-1/3">{{ attribute.name }}</div>
                <div class="attribute-value w-2/3">{{ product.attributes[attribute.code] }}</div>
            </div>
        </template>
    </div>
</template>