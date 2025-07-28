<script setup>
    defineProps(['product', 'attributes'])
</script>

<template>
    <div>
        <template v-for="attribute in attributes" :key="attribute.id">
            <div v-if="product.attributes[attribute.code] ?? false" class="flex items-center gap-4 py-2">
                <div class="w-1/2">{{ attribute.name }}</div>
                <div class="w-1/2">{{ product.attributes[attribute.code] }}</div>
            </div>
        </template>
    </div>
</template>