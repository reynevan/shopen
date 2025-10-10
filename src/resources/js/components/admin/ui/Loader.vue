<script setup>
defineProps(['loading'])
</script>

<template>
    <span class="loader" v-if="loading"></span>
</template>

<style scoped>
.loader {
    width: 48px;
    height: 48px;
    border: 5px solid #323232;
    border-bottom-color: transparent;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}

@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>