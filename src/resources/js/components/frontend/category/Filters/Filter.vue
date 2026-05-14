<script setup>
    import IconChevron from "../../../icons/IconChevron.vue";
    import {onUnmounted, ref} from "vue";
    import Button from "../../ui/Button.vue";

    const props = defineProps({
        attribute: { type: Object },
        activeFilterCount: { type: Number, default: 0 },
        isMobile: { type: Boolean, default: false },
    })

    const emits = defineEmits(['onToggle', 'onClose', 'onClear'])

    const isOpened = ref(props.isMobile && props.activeFilterCount > 0)

    const toggleOpen = () => {
        isOpened.value = !isOpened.value
    }

    onUnmounted(() => {})
</script>

<template>
    <fieldset class="filter"
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
        <div class="filter-options" v-show="isOpened">
            <div>
                <slot/>
            </div>
            <div v-if="activeFilterCount > 0" class="flex items-center justify-center pt-4 border-t border-light mt-2">
                <Button type="primary" @click.prevent="emits('onClear')">
                    Wyczyść
                </Button>
            </div>
        </div>
    </fieldset>
</template>
