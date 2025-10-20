<script setup>
    import IconChevron from "../../../icons/IconChevron.vue";
    import {onMounted, onUnmounted, ref} from "vue";

    const props = defineProps({
        attribute: { type: Object },
        activeFilterCount: { type: Number, default: 0 },
        isMobile: { type: Boolean, default: false },
    })

    const emits = defineEmits(['onToggle', 'onClose', 'onClear'])

    const isOpened = ref(props.isMobile && props.activeFilterCount > 0)
    const filterRef = ref(null)

    const toggleOpen = () => {
        isOpened.value = !isOpened.value
    }

    const handleClickOutside = (event) => {
        if (filterRef.value && !filterRef.value.contains(event.target)) {
            isOpened.value = false
        }
    }

    onMounted(() => {
        if (!props.isMobile) {
            document.addEventListener('click', handleClickOutside)
        }
    })

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside)
    })
</script>

<template>
    <fieldset class="relative sm:px-6 pb-4 sm:pb-0"
              ref="filterRef"
              :data-facet="attribute.slug"
              :data-label="attribute.name">
        <legend class="flex items-center cursor-pointer sm:px-6 sm:py-4 tracking-wider" @click="toggleOpen">
            <span v-if="activeFilterCount > 0"
                  class="mr-1 text-xs bg-accent px-2 py-1">
                {{ activeFilterCount }}
            </span>
            <span class="text-lg font-light sm:text-sm sm:font-normal">{{ attribute.name }}</span>
            <span class="transition-all duration-300 pt-1" :class="isOpened ? 'rotate-180' : ''">
                <IconChevron down/>
            </span>
        </legend>
        <div class="space-y-2 sm:px-6 sm:py-4 bg-white sm:absolute sm:top-full sm:z-1 sm:min-w-md sm:shadow-lg" v-show="isOpened">
            <div>
                <slot/>
            </div>
            <button v-if="activeFilterCount > 0"
                    @click.prevent="emits('onClear')">
                wyczyść
            </button>
        </div>
    </fieldset>
</template>