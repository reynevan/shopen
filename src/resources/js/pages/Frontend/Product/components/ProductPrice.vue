<script setup>
    defineProps(['price'])
</script>

<template>
    <div>
        <div class="text-3xl">
            {{ price.final_price}}
        </div>
        <div v-if="price.omnibus_price">
            <div class="text-sm">Najniższa cena z 30 dni przed obniżką:</div>
            <div class="text-sm line-through">{{ price.omnibus_price }}</div>
        </div>
    </div>
</template>