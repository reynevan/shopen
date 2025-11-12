<script setup>
    import {ref} from "vue";
    import ProductFormMenuItem from "@shopen/components/admin/form/menu/FormMenuItem.vue";
    import {router} from "@inertiajs/vue3";

    const props = defineProps({
        sections: { type: Array },
        activeSection: { type: String, default: 'general' },
    })
    const emits = defineEmits(['onSelect'])
    const selectedSection = ref(props.activeSection);

    const setActive = (section) => {
        emits('onSelect', section);
        selectedSection.value = section;

        const params = route().params;
        params.tab = section;

        router.get(
            route(route().current(), params),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['tab'],
                replace: true
            }
        );
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
                :disabled="section.disabled"
                @onClick="setActive(section.section)"/>
        </ul>
    </div>
</template>