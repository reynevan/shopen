<script setup>

import {onMounted, ref} from "vue";

const props = defineProps(['product', 'category']);

const breadcrumbs = ref([]);

const buildBreadcrumbs = (categoryUrl) => {
    let menuItem = document.querySelector('.nav [href="' + categoryUrl + '"]');
    if (menuItem) {
        breadcrumbs.value.unshift({
            label: menuItem.textContent,
            url: menuItem.href
        });
        menuItem = menuItem.parentNode;

        while (menuItem && (menuItem = menuItem.parentNode.closest('.nav')) !== null) {
            let link = menuItem.querySelector('a');
            breadcrumbs.value.unshift({
                label: link.textContent,
                url: link.href
            });
            menuItem = menuItem.parentNode
        }
    }
}
onMounted(() => {

    if (props.product) {

        let categoryUrl = document.referrer;

        if (categoryUrl.indexOf('?') > 0) {
            categoryUrl = categoryUrl.substring(0, categoryUrl.indexOf('?'));
        }

        const currentHostname = window.location.hostname;
        if (document.referrer.includes(currentHostname)) {
            buildBreadcrumbs(categoryUrl);
        }
        breadcrumbs.value.push({
            label: props.product.name,
            url: props.product.url
        })
    } else if (props.category) {
        let categoryUrl = window.location.href;
        buildBreadcrumbs(categoryUrl);
    }

    breadcrumbs.value.unshift({
        label: 'Home',
        url: '/'
    });
})

</script>

<template>
    <div class="breadcrumbs flex items-stretch">
        <template v-for="(element, i) in breadcrumbs">
            <span class="breadcrumb-item mr-2 h-6 block flex items-center" :class="{last: i === breadcrumbs.length - 1}">
                <a :href="element.url"  v-if="i < breadcrumbs.length - 1">{{ element.label }}</a>
                <span  v-if="i === breadcrumbs.length - 1">{{ element.label }}</span>
            </span>
            <span class="mr-2 h-6 block flex items-center">
                <svg v-if="i < breadcrumbs.length - 1" xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                </svg>
            </span>
        </template>
    </div>
</template>

<style scoped>

</style>