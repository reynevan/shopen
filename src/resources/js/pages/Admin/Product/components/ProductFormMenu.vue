<script setup>
    const model = defineModel();

    const setActive = (section) => {
        model.value = section;
    }
</script>

<template>
    <div class="aside">
        <ul>
            <li @click="setActive('general')">Główne</li>
            <li @click="setActive('prices')">Cena</li>
            <li @click="setActive('related_products')">Produkty powiązane</li>
            <li @click="setActive('attributes')">Atrybuty</li>
            <li @click="setActive('media')">Media</li>
        </ul>
    </div>
</template>