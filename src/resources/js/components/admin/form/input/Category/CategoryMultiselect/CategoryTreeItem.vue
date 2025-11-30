<script setup>
// Props
import {computed} from "vue";

const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    selectedIds: {
        type: Set
    },
    selectedId: {
        type: [String, Number],
    },
    expandedIds: {
        type: Set,
        required: true
    },
    level: {
        type: Number,
        default: 0
    },
    search: {
        type: String
    },
    path: {
        type: String
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

const isVisible = computed(() => !props.search || props.category.name.toLowerCase().indexOf(props.search.toLowerCase()) >= 0)
</script>
<template>
    <div>
        <div
            class="flex items-center py-2 pr-3 mb-1 text-sm cursor-pointer hover:bg-gray-50"
            :class="[
                selectedIds && selectedIds.has(category.id) || parseInt(selectedId) === parseInt(category.id) ? 'bg-accent-100' : ''
            ]"
            :style="{ paddingLeft: search ? '12px' : ((level * 20 + 12) + 'px') }"
            v-show="isVisible"
        >
            <div v-if="!search">
            <!-- Expand/Collapse Button -->
                <button
                    v-if="category.children && category.children.length > 0"
                    @click.stop="toggleExpand"
                    class="mr-2 p-0.5 rounded hover:bg-gray-200 focus:outline-none"
                >
                    <span class="inline-block" :class="{ 'rotate-90': expandedIds.has(category.id) }">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </button>
                <div v-else class="w-5 mr-2"></div>
            </div>
            <!-- Checkbox -->
            <input
                type="checkbox"
                :checked="selectedIds && selectedIds.has(category.id) || parseInt(selectedId) === parseInt(category.id)"
                @change="toggleCategory"
                class="mr-2 h-4 w-4 text-accent-600 focus:ring-accent-500 border-gray-300 rounded"
            />

            <!-- Category Name -->
            <span
                @click="toggleCategory"
                class="flex-1 select-none"
            >
                {{ category.name }} <span v-if="search" class="text-neutral-400 text-xs ml-4">{{ path }}</span>
            </span>
        </div>

        <!-- Children -->
        <div v-if="(expandedIds.has(category.id) || search) && category.children && category.children.length > 0">
            <CategoryTreeItem
                v-for="child in category.children"
                :key="child.id"
                :category="child"
                :selected-ids="selectedIds"
                :selected-id="selectedId"
                :expanded-ids="expandedIds"
                :level="level + 1"
                @toggle-category="$emit('toggle-category', $event)"
                @toggle-expand="$emit('toggle-expand', $event)"
                :search="search"
                :path="path + ' / ' + child.name"
            />
        </div>
    </div>
</template>