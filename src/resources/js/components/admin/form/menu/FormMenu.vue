<script setup>
    import {ref} from "vue";
    import ProductFormMenuItem from "@shopen/components/admin/form/menu/FormMenuItem.vue";

    const props = defineProps({
        sections: { type: Array },
    })
    const emits = defineEmits(['onSelect'])
    const selectedSection = ref('general');

    const setActive = (section) => {
        emits('onSelect', section);
        selectedSection.value = section;
    }
</script>

<template>
    <div class="aside">
        <ul>
            <ProductFormMenuItem
                v-for="section in sections"
                :title="section.title"
                :section="section.section"
                :selected="selectedSection === section.section"
                @onClick="setActive(section.section)"/>
        </ul>
    </div>
</template>