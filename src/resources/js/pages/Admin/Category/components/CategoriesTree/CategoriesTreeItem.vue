<script setup>
import {computed, ref} from 'vue'
import {useCategoryStore} from "@shopen/stores/admin/categoryStore.js";
import {router, usePage} from "@inertiajs/vue3";
import ActionButton from "../../../../../components/admin/ui/ActionButton.vue";
import IconLoader from "../../../../../components/icons/IconLoader.vue";

// Props
const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    canMoveUp: {
        type: Boolean,
        default: true
    },
    canMoveDown: {
        type: Boolean,
        default: true
    },
})
const page = usePage()
const categoryStore = useCategoryStore()
const isExpanded = ref(props.category.has_selected)
const loading = ref(false);

categoryStore.onExpandAll((state) => {
    isExpanded.value = state;
})

const isActive = computed(() => {
    return page.props.category?.id === props.category?.id || (!page.props.category?.id && page.props.category?.parent_id === props.category?.id)
})

const hasActiveChild = computed(() => {
    if (!props.category?.children) {
        return false
    }
    return props.category?.children.some(child => child.id === page.props.category?.id)
})

const select = () => {
    router.get(route('admin.categories.edit', props.category.id), {}, {
        only: ['category'],
        preserveState: true
    })
}

const toggleExpand = () => {
    if (props.category.children && props.category.children.length > 0) {
        isExpanded.value = !isExpanded.value
    }
}

const removeCategory = () => {
    if (!confirm(`Na pewno chcesz usunąc kategorię ${props.category.name}?`)) {
        return;
    }
    loading.value = true;
    router.delete(route('admin.categories.delete', props.category.id), {
        only: ['category'],
        preserveState: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const moveUp = () => {
    loading.value = true;
    router.put(route('admin.categories.move', props.category.id),
        {dir: 'up'},
        {
            only: ['categories'],
            preserveState: true,
            onFinish: () => {
                loading.value = false
            }
        })
}

const moveDown = () => {
    loading.value = true;
    router.put(route('admin.categories.move', props.category.id),
        {dir: 'down'},
        {
            only: ['categories'],
            preserveState: true,
            onFinish: () => {
                loading.value = false
            }
        })
}

const addSubcategory = () => {
    isExpanded.value = true
    router.get(route('admin.categories.create-subcategory', props.category.id), {}, {
        only: ['category', 'categories'],
        preserveState: true,
    })
}
</script>

<template>
    <div class="">

        <!-- Category Item -->
        <div
            class="group flex items-center justify-between text-normal transition-colors relative hover:bg-accent/50 py-1 pl-2"
            :class="isActive ? 'bg-accent' : ''"
        >
            <div class="flex items-center">
                <!-- Expand/Collapse Button -->
                <button
                    :class="[
                        isExpanded && category.children && category.children.length > 0 ? 'rotate-90' : '',
                        category.children && category.children.length > 0 ? 'cursor-pointer' : ''
                    ]"
                    @click="toggleExpand"
                    class="mr-2 p-0.5 rounded transition-all text-sm focus:outline-none hover:bg-accent-100 text-accent-600">
                    <i v-if="category.children && category.children.length > 0" class="bi bi-chevron-right"></i>
                    <i v-else class="bi bi-dash"></i>
                </button>

                <!-- Category Name -->
                <div
                    @click="select"
                    class="flex-1 select-none cursor-pointer py-1 mr-2"
                    :class="[category.is_active ? 'text-gray-900 hover:text-accent-700' : 'text-gray-400' ]">
                    {{ category.name }}
                </div>

                <IconLoader v-if="loading" size="sm"/>
            </div>
            <div class="divide-x divide-x-light hidden group-hover:flex">
                <ActionButton type="up" @click="moveUp" :disabled="!canMoveUp || loading" title="W górę"/>
                <ActionButton type="down" @click="moveDown" :disabled="!canMoveDown || loading" title="W dół"/>
                <ActionButton type="add" @click="addSubcategory" :disabled="loading" title="Dodaj podkategorię"/>
                <ActionButton type="remove" @click="removeCategory" :disabled="loading" title="Usuń"/>
            </div>

        </div>

        <!-- Children -->
        <div v-show="isExpanded && category.children && category.children.length > 0"
             class="border-l ml-4 pl-2">
            <CategoriesTreeItem
                v-for="(child, index) in category.children"
                :key="child.id"
                :category="child"
                :canMoveDown="index < category.children.length - 1"
                :canMoveUp="index > 0"
            />

        </div>
    </div>
</template>