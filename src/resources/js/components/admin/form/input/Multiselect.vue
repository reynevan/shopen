<script setup>

const value = defineModel();

const props = defineProps(['options'])

const onClick = (option) => {
    const selected = value.value || [];
    const exists = selected.includes(option.id);

    if (exists) {
        value.value = selected.filter(id => id !== option.id);
    } else {
        value.value = [...selected, option.id];
    }
};

const isSelected = (option) => {
    return (value.value || []).includes(option.id);
};
</script>

<template>

    <div class="flex flex-wrap gap-2">
        <div
            v-for="option in options"
            :key="option.id"
            @click="onClick(option)"
            class="group hover:text-accent-300 transition-colors duration-350 cursor-pointer px-4 py-2 rounded border flex items-center"
            :class="{'border-accent': isSelected(option)}">

            <i class="bi bi-circle" v-if="!isSelected(option)"></i>
            <i class="bi bi-check-circle-fill text-accent" v-if="isSelected(option)"></i>
            <div class="ml-2">
                {{ option.value }}
            </div>

        </div>
    </div>
</template>