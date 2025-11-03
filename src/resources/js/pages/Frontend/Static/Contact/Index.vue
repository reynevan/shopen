<script setup>

import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ContactForm from "./components/ContactForm.vue";

defineOptions({layout: AppLayout})

</script>

<template>
    <div class="contact">

        <ContactForm/>
    </div>
</template>