<script setup>
import {Link} from '@inertiajs/vue3'
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import IconCheck from "../../../components/icons/IconCheck.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";

defineOptions({layout: AppLayout})

</script>
<template>
    <main>
        <div class="text-center">
            <!-- Icon -->
            <div class="flex justify-center mb-4 text-green-500">
                <IconCheck size="4xl"/>
            </div>

            <h1 class="text-2xl font-normal text-green-600 mb-2">Subskrypcja potwierdzona!</h1>
            <p class="text-gray-600 mb-6">
                Dziękujemy za potwierdzenie subskrypcji newslettera.<br/>
                Od teraz będziesz otrzymywać nasze najnowsze wiadomości.
            </p>

            <Link href="/">
                <Button type="secondary">
                    Wróć na stronę główną
                </Button>
            </Link>
        </div>
    </main>
</template>
