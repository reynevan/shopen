<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import {ref} from 'vue';
import IconChevron from "../../../icons/IconChevron.vue";
import NavCategoryImage from "./NavCategoryImage.vue";

const page = usePage();
const categories = page.props.menu.categories;

const TIMEOUT = 200;

const activeLevel3 = ref(null);
const level3Timeout = ref(null);

const isOpen = ref(false);

const setActiveLevel3 = (_category) => {
    if (level3Timeout.value) {
        clearTimeout(level3Timeout.value);
    }
    level3Timeout.value = setTimeout(() => {
        activeLevel3.value = _category.id;
        level3Timeout.value = null;
    }, TIMEOUT)

};
const closeLevel3 = () => {
    if (level3Timeout.value) {
        clearTimeout(level3Timeout.value);
    }
    level3Timeout.value = setTimeout(() => {
        activeLevel3.value = null;
        level3Timeout.value = null;
    }, TIMEOUT)
}

const openMenu = () => {
    isOpen.value = true;
}

const closeMenu = () => {
    isOpen.value = false;
}
</script>

<template>
    <div class="bg-header z-100 sm:flex flex-col sm:flex-row justify-center">
        <div v-for="category in categories" :key="category.id" class="group/parent"
             @mouseover="openMenu"
             @mouseleave="closeMenu">
            <div
                class="nav-link group-hover/parent:shadow-lg group-hover/parent:underline px-4 py-2"
                :class="category.subcategories.length > 0 ? 'has-subcategories rounded-t-md' : 'rounded-md'">
                <Link :href="category.url" class="text-lg text-gray-700">
                    {{ category.name }}
                </Link>
            </div>
            <div v-if="category.subcategories && category.subcategories.length > 0"
                 class="absolute left-0 right-0 top-full z-10 w-11/12 border-t border-white bg-white pt-3 mx-auto 2xl:w-[1330px]
                     opacity-0 pointer-events-none delay-200
                     group-hover/parent:opacity-100 group-hover/parent:pointer-events-auto group-hover/parent:delay-0
                     z-[101] group-hover/parent:z-[102]
                     transition-opacity ease-out duration-200">
                <div class="flex flex-row justify-between items-stretch bg-white relative shadow-xl min-h-screen-50">
                    <ul class="w-1/3 py-6 border-r border-gray-200">
                        <li v-for="subcategory in category.subcategories"
                            :key="subcategory.id"
                            @mouseleave="closeLevel3"
                            @mouseenter="setActiveLevel3(subcategory)">
                            <Link :href="subcategory.url"
                                  :class="activeLevel3 === subcategory.id ? 'bg-accent' : ''"
                                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-accent flex items-center justify-between">
                                <span>{{ subcategory.name }}</span>
                                <span v-if="subcategory.subcategories?.length"><IconChevron right/></span>
                            </Link>
                            <div v-if="subcategory.subcategories?.length"
                                 :class="activeLevel3 === subcategory.id ?
                                    'opacity-100 pointer-events-auto delay-0' :
                                    'opacity-0 pointer-events-none'"
                                 class="absolute left-1/3 w-1/3 min-h-full top-0 transition-opacity ease-out duration-200">
                                <div v-for="subcategory2 in subcategory.subcategories" :key="subcategory2.id">
                                    <Link :href="subcategory2.url"
                                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-accent">
                                        {{ subcategory2.name }}
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="flex w-1/3 min-h-[375px]" v-if="category.image">
                        <NavCategoryImage :image="category.image" :alt="category.name"/>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <Teleport to="body">
        <transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="isOpen"
                class="fixed top-0 left-0 right-0 bottom-0 bg-black/20 z-2"
            ></div>
        </transition>
    </Teleport>
</template>