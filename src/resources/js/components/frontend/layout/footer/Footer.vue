<script setup>

import IconPhone from "@shopen/components/icons/IconPhone.vue";
import IconMail from "@shopen/components/icons/IconMail.vue";
import NewsletterForm from "@shopen/components/frontend/newsletter/NewsletterForm.vue";
import {Link} from "@inertiajs/vue3";
import Logo from "@shopen/components/frontend/ui/Logo.vue";
</script>

<template>
    <footer class="footer">
        <div class="footer-logo-wrapper flex items-center justify-center w-full">
            <Logo width="150px"/>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 justify-between mx-auto max-w-7xl px-4">
            <div class="footer-col">
                <div class="col-title">Promocje i inspiracje</div>
                <div class="col-links">
                    <div>
                        <Link prefetch class="footer-link" href="/">Kategoria 1</Link>
                    </div>
                    <div>
                        <Link prefetch class="footer-link" href="/">Kategoria 2</Link>
                    </div>
                    <div>
                        <Link prefetch class="footer-link" href="/">Kategoria 3</Link>
                    </div>
                </div>
            </div>
            <div class="footer-col">
                <div class="col-title">Shopen</div>
                <div class="col-links">
                    <div>
                        <Link prefetch class="footer-link" href="/o-nas">O nas</Link>
                    </div>
                    <div>
                        <Link prefetch class="footer-link" href="/kontakt">Kontakt</Link>
                    </div>
                    <div>
                        <Link prefetch class="footer-link" :href="route('terms-and-conditions')">Regulamin</Link>
                    </div>
                    <div>
                        <Link prefetch class="footer-link" :href="route('shipping')">Dostawy</Link>
                    </div>
                    <div>
                        <Link prefetch class="footer-link" href="/polityka-cookies">Polityka cookies</Link>
                    </div>
                </div>
            </div>
            <div class="footer-col">
                <div class="col-title">Newsletter</div>
                <NewsletterForm/>
            </div>
            <div class="footer-col">
                <div class="col-title">Kontakt</div>
                <div class="flex justify-center sm:block">
                    <div>
                        <div class="flex items-start gap-2 mb-2">
                            <div class="mt-2">
                                <IconPhone size="lg"/>
                            </div>
                            <div>
                                <a class="text-2xl" href="tel:0 700 880 717">0 700 880 717</a>
                                <p class="text-sm flex justify-between">
                                    <span class="text-neutral-400">pon - pt </span>
                                    <span>8:00 - 16:00</span>
                                </p>
                                <p class="text-sm flex justify-between">
                                    <span class="text-neutral-400">sob </span>
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
            </div>
        </div>
    </footer>
</template>