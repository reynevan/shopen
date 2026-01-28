<script setup>
    import SettingsMenuItem from "./SettingsMenuItem.vue";

    const elements = [
        {title: 'Banner informacyjny', route: 'admin.settings.top-bar.index'},
        {title: 'Metody płatności', route: 'admin.settings.payment-methods.index'},
        {title: 'Metody wysyłki', route: 'admin.settings.shipping-methods.index'},
        {title: 'Instagram', route: 'admin.settings.instagram.index'},
    ]
</script>

<template>

    <div class="aside">
        <ul class="space-y-2">
            <template v-for="(element, i) in elements" :key="i">
                <SettingsMenuItem :title="element.title" :routeName="element.route"/>
            </template>
        </ul>
    </div>
</template>