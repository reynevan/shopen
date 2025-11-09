<script setup>
import {Head, Link, router, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import IconChevron from "../icons/IconChevron.vue";
import IconHome from "../icons/IconHome.vue";

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

const showAllMobile = ref(false)
function toggleBreadcrumbs() {
    showAllMobile.value = true
}

router.on('navigate', () => {
    showAllMobile.value = false;
})

</script>

<template>
    <Head v-if="breadcrumbSchema">
        <component is="script" type="application/ld+json">
            {{ JSON.stringify(breadcrumbSchema) }}
        </component>
    </Head>

    <nav v-if="breadcrumbs && breadcrumbs.length > 1" aria-label="breadcrumb" class="breadcrumbs">
        <ol class="flex items-center text-sm min-w-0">

            <!-- 1) PIERWSZY -->
            <li class="flex items-center flex-shrink-0" v-if="breadcrumbs[0]">
                <Link
                    title="Strona główna"
                    prefetch
                    :href="breadcrumbs[0].url"
                    class="breadcrumb-element flex items-center gap-1"
                >
                    <span class="sm:hidden">
                        <IconHome size="lg" />
                    </span>
                    <span class="hidden sm:inline">{{ breadcrumbs[0].name }}</span>
                </Link>
            </li>

            <!-- separator po pierwszym -->
            <li v-if="breadcrumbs.length > 1" class="flex items-center flex-shrink-0">
                <span class="breadcrumb-separator mx-1 select-none pt-0.5" aria-hidden="true">
                  <IconChevron right size="lg" />
                </span>
            </li>

            <!-- 2) '...' — tylko na mobile i tylko gdy nie rozwinięte i są elementy pośrednie -->
            <li
                v-if="breadcrumbs.length > 2 && !showAllMobile"
                class="flex items-center flex-shrink-0 sm:hidden"
            >
                <button
                    type="button"
                    class="mx-1 breadcrumb-more-button"
                    @click="toggleBreadcrumbs"
                    aria-expanded="false"
                    aria-controls="bc-middle"
                >
                    ...
                </button>
                <span class="breadcrumb-separator mx-1 select-none pt-0.5" aria-hidden="true">
                    <IconChevron right size="lg" />
                </span>
            </li>

            <!-- 3) ELEMENTY ŚRODKOWE -->
            <li
                v-for="(item, index) in breadcrumbs.slice(1, breadcrumbs.length - 1)"
                :key="`mid-${index}`"
                :id="index === 0 ? 'bc-middle' : undefined"
                class="items-center flex-shrink-0"
                :class="['sm:flex', showAllMobile ? 'flex' : 'hidden']"
            >
                <Link
                    prefetch
                    v-if="item.url"
                    :href="item.url"
                    class="breadcrumb-element flex items-center gap-1"
                >
                    {{ item.name }}
                </Link>
                <span v-else>{{ item.name }}</span>

                <span class="breadcrumb-separator mx-1 select-none pt-0.5" aria-hidden="true">
                    <IconChevron right size="lg" />
                </span>
            </li>

            <!-- 4) OSTATNI -->
            <li v-if="breadcrumbs.length > 1" class="flex items-center min-w-0 flex-1">
                <span class="breadcrumb-element last-element truncate">
                  {{ breadcrumbs[breadcrumbs.length - 1].name }}
                </span>
            </li>
        </ol>
    </nav>
</template>