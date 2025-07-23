<template>
    <div>
        <div
            class="flex items-center px-3 py-2 mb-1 text-sm cursor-pointer hover:bg-gray-50"
            :class="{ 'bg-accent-100': selectedIds.has(category.id) }"
            :style="{ paddingLeft: (level * 20 + 12) + 'px' }"
        >
            <!-- Expand/Collapse Button -->
            <button
                v-if="category.children && category.children.length > 0"
                @click.stop="toggleExpand"
                class="mr-2 p-0.5 rounded hover:bg-gray-200 focus:outline-none"
            >
                <svg
                    class="h-3 w-3 transform transition-transform"
                    :class="{ 'rotate-90': expandedIds.has(category.id) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div v-else class="w-5 mr-2"></div>

            <!-- Checkbox -->
            <input
                type="checkbox"
                :checked="selectedIds.has(category.id)"
                @change="toggleCategory"
                class="mr-2 h-4 w-4 text-accent-600 focus:ring-accent-500 border-gray-300 rounded"
            />

            <!-- Category Name -->
            <span
                @click="toggleCategory"
                class="flex-1 select-none"
            >
        {{ category.name }}
      </span>
        </div>

        <!-- Children -->
        <div v-if="expandedIds.has(category.id) && category.children && category.children.length > 0">
            <CategoryTreeItem
                v-for="child in category.children"
                :key="child.id"
                :category="child"
                :selected-ids="selectedIds"
                :expanded-ids="expandedIds"
                :level="level + 1"
                @toggle-category="$emit('toggle-category', $event)"
                @toggle-expand="$emit('toggle-expand', $event)"
            />
        </div>
    </div>
</template>

<script setup>
// Props
const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    selectedIds: {
        type: Set,
        required: true
    },
    expandedIds: {
        type: Set,
        required: true
    },
    level: {
        type: Number,
        default: 0
    }
})

// Emits
const emit = defineEmits(['toggle-category', 'toggle-expand'])

// Methods
const toggleCategory = () => {
    emit('toggle-category', props.category)
}

const toggleExpand = () => {
    emit('toggle-expand', props.category.id)
}
</script>