<script setup>
import {ref, computed, onMounted, onUnmounted } from 'vue'
import CategoryTreeItem from './CategoryTreeItem.vue'

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

const model = defineModel()

const isDropdownOpen = ref(false)
const expandedCategoryIds = ref(new Set())

const selectedCategory = computed(() => {
    const findCategoriesById = (categories, id) => {
        for (const category of categories) {
            if (parseInt(id) === parseInt(category.id)) {
                return category
            }
            if (category.children && category.children.length > 0) {
                const result = findCategoriesById(category.children, id)
                if (result) return result;
            }
        }
    }
    return findCategoriesById(props.categories, model.value)
})

const openDropdown = () => {
    isDropdownOpen.value = true
}

const closeDropdown = () => {
    isDropdownOpen.value = false
}

const removeCategory = () => {
    model.value = null;
}

const toggleCategory = (category) => {
    if (parseInt(model.value) === parseInt(category.id)) {
        model.value = null;
    } else {
        model.value = category.id;
    }
}

const toggleExpand = (categoryId) => {
    if (expandedCategoryIds.value.has(categoryId)) {
        expandedCategoryIds.value.delete(categoryId)
    } else {
        expandedCategoryIds.value.add(categoryId)
    }
}


const handleKeydown = (event) => {
    if (event.key === 'Escape' && isDropdownOpen.value) {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
})
</script>
<template>
    <div class="relative w-full">
        <!-- Input Container -->
        <div @click="openDropdown"
             class="block w-full py-2 px-4 block w-full border border-light rounded-lg outline-none text-sm focus-within:border-accent disabled:opacity-50 disabled:pointer-events-none transition-color flex flex-wrap gap-2">
            <!-- Selected Category Tags -->
            <div v-if="selectedCategory" class="flex w-full items-center gap-2 pr-2 text-sm cursor-pointer">
                <span>{{ selectedCategory.name }}</span>
                <button
                    @click.stop="removeCategory()"
                    class="ml-1 inline-flex cursor-pointer items-center rounded-full text-accent-400 hover:bg-accent-200 hover:text-accent-600 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <span v-if="!selectedCategory" class="text-gray-500 select-none">
                Wybierz kategorię...
            </span>
        </div>

        <!-- Dropdown -->
        <div v-if="isDropdownOpen"
             class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
            <div class="py-1">
                <CategoryTreeItem
                    v-for="category in categories"
                    :key="category.id"
                    :category="category"
                    :selected-id="model"
                    :expanded-ids="expandedCategoryIds"
                    @toggle-category="toggleCategory"
                    @toggle-expand="toggleExpand"
                />
            </div>
        </div>

        <!-- Overlay to close dropdown -->
        <div
            v-if="isDropdownOpen"
            @click="closeDropdown"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>