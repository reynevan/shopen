<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const breadcrumbs = computed(() => page.props.breadcrumbs);
const baseUrl = computed(() => page.props.ziggy?.url || window.location.origin);

const breadcrumbSchema = computed(() => {
    if (!breadcrumbs.value || breadcrumbs.value.length === 0) {
        return null;
    }

    const items = breadcrumbs.value.map((item, index) => {
        const listItem = {
            '@type': 'ListItem',
            position: index + 1,
            name: item.name,
        };

        if (item.url) {
            listItem.item = new URL(item.url, baseUrl.value).href;
        }

        return listItem;
    });

    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: items,
    };
});
</script>

<template>
    <Head v-if="breadcrumbSchema">
        <component is="script" type="application/ld+json">
            {{ JSON.stringify(breadcrumbSchema) }}
        </component>
    </Head>

    <nav v-if="breadcrumbs && breadcrumbs.length" aria-label="breadcrumb" class="breadcrumbs">
        <ol class="flex flex-wrap items-center text-sm">
            <li v-for="(item, index) in breadcrumbs" :key="index" class="flex items-center">
                <span v-if="index > 0" class="mx-1 select-none text-neutral-400 font-thint" aria-hidden="true">|</span>
                <Link
                    v-if="item.url"
                    :href="item.url"
                    class="hover:text-black font-thin">
                    {{ item.name }}
                </Link>
                <span v-else>
                    {{ item.name }}
                </span>
            </li>
        </ol>
    </nav>
</template>