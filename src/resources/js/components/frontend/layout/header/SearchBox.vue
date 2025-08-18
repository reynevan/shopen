<script setup>

import {useForm, usePage} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconSearch from "@shopen/components/icons/IconSearch.vue";

const page = usePage();

const form = useForm({
    q: page.props.query ?? ''
})

const submit = () => {
    form.get(route('search.index'))
}
</script>

<template>
    <form @submit.prevent="submit" class="w-full">
        <div class="flex items-stretch w-full gap-2 relative">
            <input type="text" v-model="form.q" class="pr-24">
            <Button role="submit" type="ghost" class="absolute right-0 h-[100%-1px]">
                <IconSearch size="2xl"/>
            </Button>
        </div>
    </form>
</template>