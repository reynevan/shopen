<script setup>
    import IconChevron from "../../../icons/IconChevron.vue";
    import {onMounted, onUnmounted, ref} from "vue";
    import Button from "../../ui/Button.vue";

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
    <fieldset class="filter relative"
              ref="filterRef"
              :data-facet="attribute.slug"
              :data-label="attribute.name">
        <legend class="filter-label" @click="toggleOpen">
            <span v-if="activeFilterCount > 0"
                  class="active-options-count">
                {{ activeFilterCount }}
            </span>
            <span class="filter-name">{{ attribute.name }}</span>
            <span class="transition-all duration-300 filter-arrow" :class="isOpened ? 'rotate-180' : ''">
                <IconChevron down/>
            </span>
        </legend>
        <div class="filter-options sm:absolute sm:top-full sm:z-10" v-show="isOpened">
            <div>
                <slot/>
            </div>
            <div v-if="activeFilterCount > 0" class="flex items-center justify-center sm:border-t sm:border-light sm:mt-4 pt-4">
            <Button type="primary" @click.prevent="emits('onClear')">
                Wyczyść
            </Button>
            </div>
        </div>
    </fieldset>
</template>