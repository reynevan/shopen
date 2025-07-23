<script setup>
import {computed} from 'vue'
import {useCategoryStore} from "@shopen/stores/admin/categoryStore.js";
import {router, usePage} from "@inertiajs/vue3";

// Props
const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    },
    parentId: {
        type: [Number, null],
        default: null
    },
    expandedIds: {
        type: Set,
        required: true
    },
    level: {
        type: Number,
        default: 0
    },
    draggingId: {
        type: [Number, null],
        default: null
    },
    dragOverInfo: {
        type: Object,
        default: null
    }
})

// Emits
const emit = defineEmits([
    'toggle-expand',
    'drag-start',
    'drag-end',
    'drag-over',
    'drag-enter',
    'drag-leave',
    'drop'
])
const page = usePage();
const activeCategoryId = computed(() => page.props.category?.id);
const categoryStore = useCategoryStore();

// Computed
const isDragging = computed(() => props.draggingId === props.category.id)
const isDropTarget = computed(() =>
    props.dragOverInfo?.parentId === props.category.id && props.dragOverInfo?.type === 'position'
)
const isNestTarget = computed(() =>
    props.dragOverInfo?.parentId === props.category.id && props.dragOverInfo?.type === 'nest'
)

// Methods
const toggleExpand = () => {
    if (props.category.children && props.category.children.length > 0) {
        emit('toggle-expand', props.category.id)
    }
}
const select = () => {
    router.get(route('admin.categories.edit', props.category.id), {}, {
        preserveScroll: true,
        only: ['category']
    })
}
const handleDragStart = (event) => {
    event.dataTransfer.effectAllowed = 'move'
    event.dataTransfer.setData('text/plain', props.category.id.toString())
    emit('drag-start', props.category.id)
}

// Category drag handlers (for nesting)
const handleDragOverCategory = (event) => {
    if (props.draggingId && props.draggingId !== props.category.id && props.category.is_active !== false) {
        event.preventDefault()
        emit('drag-over', event, props.category.id, 0, 'nest')
    }
}

const handleDragEnterCategory = (event) => {
    if (props.draggingId && props.draggingId !== props.category.id && props.category.is_active !== false) {
        event.preventDefault()
        emit('drag-enter', event, props.category.id, 0, 'nest')
    }
}

const handleDragLeaveCategory = () => {
    emit('drag-leave')
}

const handleDropOnCategory = (event) => {
    if (props.draggingId && props.draggingId !== props.category.id && props.category.is_active !== false) {
        event.preventDefault()
        emit('drop', event, props.category.id, 0, 'nest')
    }
}
</script>

<template>
    <div>
        <!-- Drop zone before item -->
        <div
            v-if="!isDragging"
            @dragover.prevent="$emit('drag-over', $event, parentId, index, 'position')"
            @dragenter.prevent="$emit('drag-enter', $event, parentId, index, 'position')"
            @dragleave="$emit('drag-leave')"
            @drop="$emit('drop', $event, parentId, index, 'position')"
            :class="[
                'h-2 mx-3 transition-colors',
                dragOverInfo?.parentId === parentId && dragOverInfo?.index === index && dragOverInfo?.type === 'position'
                  ? 'bg-accent-200 border-2 border-accent-400'
                  : 'hover:bg-gray-50'
            ]"
        ></div>

        <!-- Category Item -->
        <div
            :draggable="true"
            @dragstart="handleDragStart"
            @dragend="$emit('drag-end')"
            @dragover.prevent="handleDragOverCategory"
            @dragenter.prevent="handleDragEnterCategory"
            @dragleave="handleDragLeaveCategory"
            @drop="handleDropOnCategory"
            class="flex items-center px-3 text-sm transition-colors relative"
            :class="[
                categoryStore.selectedCategory.id === category.id ? 'bg-accent-100 text-accent-600' : '',
                category.is_active ? 'text-gray-900' : 'text-gray-400',
                isDragging ? 'opacity-50' : 'hover:bg-gray-50',
                isDropTarget ? 'bg-accent-50' : '',
                isNestTarget ? 'bg-accent-100' : '',
                activeCategoryId === category.id ? 'bg-accent-200' : ''
           ]"
            :style="{ paddingLeft: (level * 20 + 12) + 'px' }">
            <!-- Drag Handle -->
            <div class="mr-2 text-gray-400 hover:text-accent-500">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M7 2a1 1 0 00-1 1v1H4a1 1 0 000 2h2v1a1 1 0 002 0V6h2a1 1 0 100-2H8V3a1 1 0 00-1-1zM7 8a1 1 0 00-1 1v1H4a1 1 0 100 2h2v1a1 1 0 002 0v-1h2a1 1 0 100-2H8V9a1 1 0 00-1-1zM7 14a1 1 0 00-1 1v1H4a1 1 0 100 2h2v1a1 1 0 002 0v-1h2a1 1 0 100-2H8v-1a1 1 0 00-1-1z"></path>
                </svg>
            </div>

            <!-- Expand/Collapse Button -->
            <button
                v-if="category.children && category.children.length > 0"
                @click="toggleExpand"
                class="mr-2 p-0.5 rounded transition-colors focus:outline-none hover:bg-accent-100 text-accent-600">
                <svg
                    class="h-4 w-4 transform transition-transform"
                    :class="{ 'rotate-90': expandedIds.has(category.id) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div v-else class="w-6 mr-2"></div>

            <!-- Category Icon -->
            <div class="mr-2">
                <svg
                    v-if="category.children && category.children.length > 0"
                    class="h-4 w-4"
                    :class="[
                        category.is_active ? 'text-accent-500' : 'text-gray-300'
                    ]"
                    fill="currentColor"
                    viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                </svg>
                <svg
                    v-else
                    class="h-4 w-4"
                    :class="[
                        category.is_active ? 'text-accent-400' : 'text-gray-300'
                    ]"
                    fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-5L9 2H4z"
                          clip-rule="evenodd"></path>
                </svg>
            </div>

            <!-- Category Name -->
            <span
                @click="select"
                class="flex-1 select-none cursor-pointer font-medium"
                :class="[category.is_active ? 'text-gray-900 hover:text-accent-700' : 'text-gray-400' ]">
                {{ category.name }}
             </span>

            <!-- Nest indicator -->
            <div v-if="isNestTarget"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-accent-500">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                          clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>

        <!-- Children -->
        <div v-if="expandedIds.has(category.id) && category.children && category.children.length > 0"
            class="border-l border-gray-200"
            :style="{ marginLeft: (level * 20 + 16) + 'px' }">
            <CategoriesTreeItem
                v-for="(child, childIndex) in category.children"
                :key="child.id"
                :category="child"
                :index="childIndex"
                :parent-id="category.id"
                :expanded-ids="expandedIds"
                :level="level + 1"
                :dragging-id="draggingId"
                :drag-over-info="dragOverInfo"
                @toggle-expand="$emit('toggle-expand', $event)"
                @drag-start="$emit('drag-start', $event)"
                @drag-end="$emit('drag-end')"
                @drag-over="(...args) => $emit('drag-over', ...args)"
                @drag-enter="(...args) => $emit('drag-enter', ...args)"
                @drag-leave="$emit('drag-leave')"
                @drop="(...args) => $emit('drop', ...args)"
            />

            <!-- Drop zone at the end of children -->
            <div
                v-if="draggingId && draggingId !== category.id"
                @dragover.prevent="$emit('drag-over', $event, category.id, category.children.length, 'position')"
                @dragenter.prevent="$emit('drag-enter', $event, category.id, category.children.length, 'position')"
                @dragleave="$emit('drag-leave')"
                @drop="$emit('drop', $event, category.id, category.children.length, 'position')"
                :class="[
                    'h-2 mx-3 transition-colors',
                    dragOverInfo?.parentId === category.id && dragOverInfo?.index === category.children.length && dragOverInfo?.type === 'position'
                    ? 'bg-accent-200 border-2 border-accent-400'
                    : 'hover:bg-gray-50'
                ]"
                :style="{ marginLeft: ((level + 1) * 20 + 12) + 'px' }"></div>
        </div>
    </div>
</template>