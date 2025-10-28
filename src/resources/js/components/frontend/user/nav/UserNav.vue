<script setup>

import IconReceipt from "@shopen/components/icons/IconReceipt.vue";
import IconReceiptFull from "@shopen/components/icons/IconReceiptFull.vue";
import UserNavItem from "@shopen/components/frontend/user/nav/UserNavItem.vue";
import IconLocationFull from "@shopen/components/icons/IconLocationFull.vue";
import IconLocation from "@shopen/components/icons/IconLocation.vue";
import IconHeartFull from "@shopen/components/icons/IconHeartFull.vue";
import IconHeart from "@shopen/components/icons/IconHeart.vue";
import IconSettingsFull from "@shopen/components/icons/IconSettingsFull.vue";
import IconSettings from "@shopen/components/icons/IconSettings.vue";
</script>

<template>
    <nav class="flex-shrink-0 w-full sm:w-60 my-6 pb-6 border-b sm:border-none">
        <ul class="space-y-2">
            <UserNavItem label="Zamówienia" route-name="user.orders.index">
                <template #iconActive>
                    <IconReceiptFull/>
                </template>
                <template #iconInactive>
                    <IconReceipt/>
                </template>
            </UserNavItem>

            <UserNavItem label="Dane do zamówień" route-name="user.addresses.index">
                <template #iconActive>
                    <IconLocationFull/>
                </template>
                <template #iconInactive>
                    <IconLocation/>
                </template>
            </UserNavItem>

            <UserNavItem label="Listy zakupowe" route-name="user.shopping-lists.index">
                <template #iconActive>
                    <IconHeartFull/>
                </template>
                <template #iconInactive>
                    <IconHeart/>
                </template>
            </UserNavItem>

            <UserNavItem label="Ustawienia konta" route-name="user.settings.index">
                <template #iconActive>
                    <IconSettingsFull/>
                </template>
                <template #iconInactive>
                    <IconSettings/>
                </template>
            </UserNavItem>
        </ul>
    </nav>
</template>