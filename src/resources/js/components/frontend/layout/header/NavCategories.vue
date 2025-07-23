<script setup>

import {Link, usePage} from "@inertiajs/vue3";

const page = usePage();
const categories = page.props.menu.categories;
</script>

<template>
    <div class="navigation sm:flex justify-center flex-col sm:flex-row">
        <div v-for="category in categories" class="p-2 group/category nav">

            <Link :href="category.url" class="nav-category-link {{ $block->getCssClasses($category) }}" >
                {{ category.name }}
            </Link>
            <div v-if="category.subcategories && category.subcategories.length > 0" class="nav-category flex invisible absolute z-50 group-hover/category:visible ">
                <div v-for="subcategory in category.subcategories" class="group/subcategory nav">
                    <Link :href="subcategory.url" class="nav-subcategory-link block {{ $block->getCssClasses($subcategory) }}">
                        {{ subcategory.name }}
                    </Link>
                    <div v-if="subcategory.subcategories && subcategory.subcategories.length">
                        <div v-for="subcategory2 in subcategory.subcategories" class="nav">
                            <Link :href="subcategory2.url" class="nav-subcategory-2-link block {{ $block->getCssClasses($subcategory2) }}">
                                {{ subcategory2.name }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>