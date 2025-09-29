<script setup>
import { ref, watch } from 'vue'
import CategoriesTreeItem from '@shopen/pages/Admin/Category/components/CategoriesTree/CategoriesTreeItem.vue'

// Props
const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

</script>
<template>
    <div class="w-full">
        <div class="py-2">
            <CategoriesTreeItem
                v-for="(category, index) in categories"
                :key="category.id"
                :category="category"
                :canMoveDown="index < categories.length - 1"
                :canMoveUp="index > 0"
            />
        </div>
    </div>
</template>