<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import Heading from "../components/Heading.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import ShoppingListThumbnail from "./components/ShoppingListThumbnail.vue";
import {ref} from "vue";
import Input from "../../../../components/frontend/input/Input.vue";

defineOptions({layout: UserPanelLayout})

const props = defineProps({
    lists: Array,
});

const form = useForm({
    name: '',
});

const creatingList = ref(false);

const createList = () => {
    if (!form.name) {
        return;
    }
    form.post(route('user.shopping-lists.store'), {
        onSuccess: () => {
            creatingList.value = false;
            form.reset();
        },
    });
};

</script>

<template>
    <Heading title="Listy zakupowe">
        <template #action>
            <Button @click="creatingList = true">Dodaj listę</Button>
        </template>
    </Heading>
    <Head title="Moje listy zakupowe"/>
    <div>
        <form v-if="creatingList" @submit.prevent="createList" class="mb-8 p-4 border-y rounded">
            <h3 class="text-lg mb-2">Stwórz nową listę</h3>
            <div class="flex gap-2">
                <Input v-model="form.name" placeholder="Nazwa listy"/>
                <Button type="primary" role="submit" :disabled="form.processing">
                    Utwórz
                </Button>
                <Button type="ghost" role="button" :disabled="form.processing" @click="creatingList = false">
                    Anuluj
                </Button>
            </div>
        </form>

        <div class="space-y-6">
            <div v-if="!lists.length && !creatingList">
                <p>Nie masz jeszcze żadnych list zakupowych.</p>
            </div>
            <ShoppingListThumbnail v-for="list in lists" :key="list.id" :list="list"/>
        </div>
    </div>
</template>