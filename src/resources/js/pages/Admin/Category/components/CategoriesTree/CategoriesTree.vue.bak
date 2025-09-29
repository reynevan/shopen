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

// Emit
const emit = defineEmits(['update:categories', 'move:category'])

// Reactive data
const localCategories = ref(JSON.parse(JSON.stringify(props.categories))) // Deep clone
const expandedCategoryIds = ref(new Set())
const draggingId = ref(null)
const dragOverInfo = ref(null)

// Watch for external changes
watch(() => props.categories, (newCategories) => {
    localCategories.value = JSON.parse(JSON.stringify(newCategories)) // Deep clone
}, { deep: true })

// Methods
const toggleExpand = (categoryId) => {
    if (expandedCategoryIds.value.has(categoryId)) {
        expandedCategoryIds.value.delete(categoryId)
    } else {
        expandedCategoryIds.value.add(categoryId)
    }
}

const expandAll = () => {
    const getAllCategoryIds = (categories) => {
        const ids = []
        categories.forEach(category => {
            if (category.children && category.children.length > 0) {
                ids.push(category.id)
                ids.push(...getAllCategoryIds(category.children))
            }
        })
        return ids
    }

    const allIds = getAllCategoryIds(localCategories.value)
    expandedCategoryIds.value = new Set(allIds)
}

const collapseAll = () => {
    expandedCategoryIds.value = new Set()
}

// Helper function to find category and its parent recursively
const findCategoryInfo = (categories, categoryId, parentId = null) => {
    for (let i = 0; i < categories.length; i++) {
        const category = categories[i]
        if (category.id === categoryId) {
            return {
                category: { ...category }, // Clone the category
                parentId,
                index: i,
                parentArray: categories
            }
        }
        if (category.children && category.children.length > 0) {
            const found = findCategoryInfo(category.children, categoryId, category.id)
            if (found) return found
        }
    }
    return null
}

// Helper function to find target parent array
const findTargetArray = (categories, parentId) => {
    if (parentId === null) {
        return localCategories.value
    }

    const findParent = (cats) => {
        for (const cat of cats) {
            if (cat.id === parentId) {
                if (!cat.children) cat.children = []
                return cat.children
            }
            if (cat.children) {
                const found = findParent(cat.children)
                if (found) return found
            }
        }
        return null
    }

    return findParent(categories)
}

// Move category function
const moveCategory = (draggedId, targetParentId, targetIndex, type) => {
    // Find dragged category info
    const draggedInfo = findCategoryInfo(localCategories.value, draggedId)
    if (!draggedInfo) return false

    // If dropping "on" a category (nesting), adjust target
    if (type === 'nest') {
        targetIndex = 0
    }

    // Remove from current position
    draggedInfo.parentArray.splice(draggedInfo.index, 1)

    // Find target array
    const targetArray = findTargetArray(localCategories.value, targetParentId)
    if (!targetArray) return false

    // Adjust index if moving within same parent and to a later position
    if (draggedInfo.parentId === targetParentId && draggedInfo.index < targetIndex) {
        targetIndex--
    }

    // Insert at new position
    targetArray.splice(targetIndex, 0, draggedInfo.category)

    return true
}

// Drag and Drop handlers
const handleDragStart = (categoryId) => {
    draggingId.value = categoryId
}

const handleDragEnd = () => {
    draggingId.value = null
    dragOverInfo.value = null
}

const handleDragOver = (event, parentId, index, type = 'position') => {
    event.preventDefault()
    if (draggingId.value && draggingId.value !== parentId) {
        dragOverInfo.value = { parentId, index, type }
    }
}

const handleDragEnter = (event, parentId, index, type = 'position') => {
    event.preventDefault()
    if (draggingId.value && draggingId.value !== parentId) {
        dragOverInfo.value = { parentId, index, type }
    }
}

const handleDragLeave = () => {
    setTimeout(() => {
        if (dragOverInfo.value) {
            dragOverInfo.value = null
        }
    }, 50)
}

const handleDrop = async (event, parentId, index, type = 'position') => {
    event.preventDefault()

    if (!draggingId.value || !dragOverInfo.value) return

    const draggedCategoryId = draggingId.value
    let targetParentId = parentId
    let targetIndex = index

    // If dropping "on" a category, add to its children
    if (type === 'nest') {
        targetParentId = parentId
        targetIndex = 0
    }

    // Don't drop on itself or its children
    if (draggedCategoryId === targetParentId || isDescendant(draggedCategoryId, targetParentId)) {
        draggingId.value = null
        dragOverInfo.value = null
        return
    }

    // Move category locally
    const success = moveCategory(draggedCategoryId, targetParentId, targetIndex, type)

    if (success) {
        // Update sort indexes
        updateSortIndexes(localCategories.value)

        // Emit updated categories
        emit('update:categories', [...localCategories.value])

        // Call API
        try {
            emit('move:category',localCategories.value);
            //await props.categoriesStore.moveCategory(draggedCategoryId, targetParentId, targetIndex)
        } catch (error) {
            console.error('Error moving category:', error)
            // Optionally revert changes on error
            localCategories.value = JSON.parse(JSON.stringify(props.categories))
        }
    }

    // Reset drag state
    draggingId.value = null
    dragOverInfo.value = null
}

// Helper function to update sort indexes
const updateSortIndexes = (categories) => {
    const updateLevel = (cats) => {
        cats.forEach((cat, index) => {
            cat.sort_index = index
            if (cat.children && cat.children.length > 0) {
                updateLevel(cat.children)
            }
        })
    }
    updateLevel(categories)
}

// Helper function to check if target is a descendant of source
const isDescendant = (sourceId, targetId) => {
    const sourceInfo = findCategoryInfo(localCategories.value, sourceId)
    if (!sourceInfo || !sourceInfo.category.children) return false

    const checkDescendant = (children, targetId) => {
        for (const child of children) {
            if (child.id === targetId) return true
            if (child.children && checkDescendant(child.children, targetId)) return true
        }
        return false
    }

    return checkDescendant(sourceInfo.category.children, targetId)
}
</script>
<template>
    <div class="w-full">
        <!-- Control Buttons -->
        <div class="flex gap-2 mb-4 p-3 bg-gray-50 rounded-md border">
            <button
                @click="expandAll"
                class="px-3 py-1.5 text-sm font-medium text-accent-700 bg-accent-100 hover:bg-accent-200 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-accent-500" >
                Rozwiń wszystkie
            </button>
            <button @click="collapseAll"
                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                Zwiń wszystkie
            </button>
        </div>

        <!-- Categories Tree -->
        <div class="border border-gray-300 rounded-md bg-white">
            <div class="py-2">
                <!-- Drop zone at the top -->
                <div
                    @dragover.prevent="handleDragOver($event, null, 0, 'position')"
                    @dragenter.prevent="handleDragEnter($event, null, 0, 'position')"
                    @dragleave="handleDragLeave"
                    @drop="handleDrop($event, null, 0, 'position')"
                    :class="[
                        'h-2 mx-3 transition-colors',
                        dragOverInfo?.parentId === null && dragOverInfo?.index === 0 && dragOverInfo?.type === 'position'
                          ? 'bg-accent-200 border-2 border-accent-400'
                          : 'hover:bg-gray-100'
                    ]"
                ></div>

                <CategoriesTreeItem
                    v-for="(category, index) in localCategories"
                    :key="category.id"
                    :category="category"
                    :index="index"
                    :parent-id="null"
                    :expanded-ids="expandedCategoryIds"
                    :dragging-id="draggingId"
                    :drag-over-info="dragOverInfo"
                    @toggle-expand="toggleExpand"
                    @drag-start="handleDragStart"
                    @drag-end="handleDragEnd"
                    @drag-over="handleDragOver"
                    @drag-enter="handleDragEnter"
                    @drag-leave="handleDragLeave"
                    @drop="handleDrop"
                />
            </div>
        </div>
    </div>
</template>