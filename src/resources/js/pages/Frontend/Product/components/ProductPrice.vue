<script setup>
    defineProps(['price'])
</script>

<template>
    <div>
        <div v-if="price.omnibus_price">
            <div class="omnibus-price-label">Najniższa cena z 30 dni przed obniżką:</div>
            <div class="omnibus-price line-through">{{ price.omnibus_price }}</div>
        </div>
        <div class="product-final-price">
            {{ price.final_price}}
        </div>
    </div>
</template>