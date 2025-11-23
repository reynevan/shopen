<script setup>
import {ref, computed, onMounted, onUnmounted, watch} from 'vue'
import CategoryTreeItem from './CategoryTreeItem.vue'

// Props
const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

// Model - tablica ID kategorii
const model = defineModel({
    type: Array,
    default: () => []
})

// Reactive data
const isDropdownOpen = ref(false)
const selectedCategoryIds = ref(new Set(model.value))
const expandedCategoryIds = ref(new Set())

// Watch for external model changes
watch(model, (newValue) => {
    selectedCategoryIds.value = new Set(newValue)
}, {deep: true})

// Computed - znajdź pełne obiekty kategorii na podstawie ID
const selectedCategories = computed(() => {
    const result = []
    const findCategoriesById = (categories, ids) => {
        for (const category of categories) {
            if (ids.has(category.id)) {
                result.push(category)
            }
            if (category.children && category.children.length > 0) {
                findCategoriesById(category.children, ids)
            }
        }
    }
    findCategoriesById(props.categories, selectedCategoryIds.value)
    return result
})

// Methods
const openDropdown = () => {
    isDropdownOpen.value = true
}

const closeDropdown = () => {
    isDropdownOpen.value = false
}

const removeCategory = (categoryId) => {
    selectedCategoryIds.value.delete(categoryId)
    updateModel()
}

const toggleCategory = (category) => {
    if (selectedCategoryIds.value.has(category.id)) {
        selectedCategoryIds.value.delete(category.id)
    } else {
        selectedCategoryIds.value.add(category.id)
    }
    updateModel()
}

const toggleExpand = (categoryId) => {
    if (expandedCategoryIds.value.has(categoryId)) {
        expandedCategoryIds.value.delete(categoryId)
    } else {
        expandedCategoryIds.value.add(categoryId)
    }
}

const updateModel = () => {
    // Emituj tylko tablicę ID
    model.value = Array.from(selectedCategoryIds.value)
}

// Handle ESC key to close dropdown
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
             class="min-h-[46px] block w-full py-2.5 sm:py-3 px-4 block w-full border border-gray-200 rounded-lg outline-none sm:text-sm focus-within:border-accent disabled:opacity-50 disabled:pointer-events-none transition-color flex flex-wrap gap-2">
            <!-- Selected Category Tags -->
            <div v-for="category in selectedCategories" :key="category.id"
                 class="inline-flex items-center pr-2 pl-4 py-1 text-sm font-medium bg-accent-100 text-accent-800 border border-accent-300 rounded">
                <span>{{ category.name }}</span>
                <button
                    @click.stop="removeCategory(category.id)"
                    class="ml-1 inline-flex cursor-pointer items-center rounded-full text-accent-400 hover:bg-accent-200 hover:text-accent-600 focus:outline-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Placeholder when no categories selected -->
            <span v-if="selectedCategories.length === 0" class="text-gray-500 select-none">
                Wybierz kategorie...
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
                    :selected-ids="selectedCategoryIds"
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