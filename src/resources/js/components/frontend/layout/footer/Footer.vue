<script setup>

import IconPhone from "../../../icons/IconPhone.vue";
import IconMail from "../../../icons/IconMail.vue";
</script>

<template>
    <footer class="footer py-10 px-6 border-t border-light ">
        <div class="flex flex-col sm:flex-row gap-4 justify-between mx-auto max-w-7xl px-4">
            <div>
                <div class="font-semibold text-xl">Promocje i inspiracje</div>
                <div>Kategoria 1</div>
                <div>Kategoria 2</div>
                <div>Kategoria 3</div>
            </div>
            <div>
                <div class="font-semibold text-xl">Shopen</div>
                <div>O nas</div>
                <div>Kontakt</div>
                <div>Regulamin</div>
                <div>Polityka prywatności</div>
                <div>Polityka cookies</div>
            </div>
            <div>
                <div class="font-semibold text-xl">Kontakt</div>
                <div class="flex items-start gap-2 mb-2">
                    <div class="mt-2">
                        <IconPhone size="lg"/>
                    </div>
                    <div>
                        <a class="text-2xl" href="tel:0 700 880 717">0 700 880 717</a>
                        <p class="text-sm flex justify-between">
                            <span class="text-neutral-500">pon - pt </span>
                            <span>8:00 - 16:00</span>
                        </p>
                        <p class="text-sm flex justify-between">
                            <span class="text-neutral-500">sob </span>
                            <span>8:00 - 16:00</span>
                        </p>
                    </div>

                </div>
                <div class="flex items-center gap-2">
                    <IconMail size="lg"/>
                    <a href="mailto:kontakt@entrech.pl">kontakt@entrech.pl</a>
                </div>
            </div>
        </div>
    </footer>
</template>