<script setup>
import {ref, reactive, computed, onMounted} from 'vue'
import BaseModal from "@shopen/components/frontend/ui/BaseModal.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import Toggle from "@shopen/components/frontend/input/Toggle.vue";

// dane o stronie (np. ID GA z propsów Inertia)
import {usePage} from "@inertiajs/vue3";

const page = usePage();

const showBanner = ref(true)
const showAdvanced = ref(false)

const consents = reactive({
    essential: true,
    marketing: false,
    analytics: false
})

const showDetails = reactive({
    essential: false,
    marketing: false,
    analytics: false
})

const cookieGroups = computed(() => [
    {
        id: 'essential',
        name: 'Podstawowe',
        description: 'Niezbędne do funkcjonowania strony.',
        required: true,
        cookies: []
    },
    {
        id: 'marketing',
        name: 'Marketing',
        description: 'Pliki cookie do personalizacji reklam.',
        required: false,
        cookies: [
            {name: '_gcl_au', description: 'Google Ads tracking', lifetime: '90 dni', type: '3rd party'}
        ]
    },
    {
        id: 'analytics',
        name: 'Analityka',
        description: 'Pomagają zrozumieć korzystanie z serwisu.',
        required: false,
        cookies: [
            {name: '_ga', description: 'Google Analytics identyfikator', lifetime: '2 lata', type: '3rd party'},
            {name: '_gid', description: 'Google Analytics sesja', lifetime: '24 godziny', type: '3rd party'}
        ]
    }
])

// 🔹 Aktualizacja stanów Consent Mode
const updateGoogleConsent = () => {
    if (typeof window !== 'undefined' && window.gtag) {
        window.gtag('consent', 'update', {
            'ad_storage': consents.marketing ? 'granted' : 'denied',
            'ad_user_data': consents.marketing ? 'granted' : 'denied',
            'ad_personalization': consents.marketing ? 'granted' : 'denied',
            'analytics_storage': consents.analytics ? 'granted' : 'denied'
        })
    }
}

// Akcje
const toggleConsent = (type) => {
    if (type !== 'essential') consents[type] = !consents[type]
}
const toggleCookieDetails = (type) => showDetails[type] = !showDetails[type]

const acceptAll = () => {
    cookieGroups.value.forEach(g => {
        if (!g.required) consents[g.id] = true
    })
    saveConsents()
    updateGoogleConsent()
    hideBanner()
}
const acceptSelected = () => {
    saveConsents();
    updateGoogleConsent();
    hideBanner()
}
const rejectAll = () => {
    cookieGroups.value.forEach(g => {
        if (!g.required) consents[g.id] = false
    })
    saveConsents()
    updateGoogleConsent()
    hideBanner()
}

// LocalStorage
const saveConsents = () => {
    localStorage.setItem('cookie_consent', JSON.stringify({
        ...consents,
        timestamp: new Date().toISOString(),
        version: '2.0'
    }))
}
const checkExistingConsent = () => {
    const saved = localStorage.getItem('cookie_consent')
    if (saved) {
        const parsed = JSON.parse(saved)
        if (parsed.version === '2.0') {
            Object.keys(consents).forEach(k => {
                if (parsed[k] !== undefined) consents[k] = parsed[k]
            })
            showBanner.value = false
            updateGoogleConsent()
        }
    }
}
const hideBanner = () => {
    showBanner.value = false
}

// Globalny trigger
const showConsentBanner = () => {
    showBanner.value = true;
    showAdvanced.value = true
}
onMounted(() => {
    checkExistingConsent()
    if (typeof window !== 'undefined') {
        window.showConsentBanner = showConsentBanner
    }
})
</script>

<template>
    <div v-if="showBanner && !showAdvanced" class="cookies-modal">
        <div class="px-6 py-6 text-center max-w-xl">
            <p class="mb-6">
                Używamy plików cookie, aby zapewnić najlepsze doświadczenia.
                Możesz zaakceptować wszystkie lub przejść do ustawień zaawansowanych.
            </p>
            <div class="flex justify-center gap-4">
                <Button type="ghost" size="lg" @click="showAdvanced = true">
                    Ustawienia zaawansowane
                </Button>
                <Button type="primary" size="lg" @click="acceptAll">
                    Akceptuję wszystkie
                </Button>
            </div>
        </div>
    </div>
    <BaseModal v-if="showBanner && showAdvanced" :show="showBanner" @onClose="showBanner = false">
        <template #header>
            <h2 class="text-xl font-semibold">Ustawienia plików cookie</h2>
        </template>
        <template #default>
            <div>
                <div class="p-6 space-y-4">
                    <div v-for="group in cookieGroups" :key="group.id" class="border rounded-lg">
                        <div class="flex justify-between gap-4 p-4 items-center">
                            <div>
                                <h3 class="font-medium">{{ group.name }}</h3>
                                <p class="text-sm text-gray-600">{{ group.description }}</p>
                                <a @click="toggleCookieDetails(group.id)" class="text-sm cursor-pointer">
                                    {{ showDetails[group.id] ? 'Ukryj' : 'Szczegóły' }}
                                </a>
                            </div>
                            <div class="flex items-center">
                                <Toggle v-model="consents[group.id]" :disabled="group.required"/>
                            </div>
                        </div>
                        <div v-if="showDetails[group.id]" class="px-4 py-4 border-t border-light space-y-4">
                            <div v-for="cookie in group.cookies" :key="cookie.name"
                                 class="bg-gray-50 p-3 rounded text-sm">
                                <div class="flex">
                                    <div class="w-24">Plik cookie</div>
                                    <div class="text-left font-light">{{ cookie.name }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-24">Opis</div>
                                    <div class="text-left font-light">{{ cookie.description }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-24">Czas trwania</div>
                                    <div class="text-left font-light">{{ cookie.lifetime }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-24">Rodzaj</div>
                                    <div class="text-left font-light">{{ cookie.type }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #buttons>
            <div class="flex justify-between w-full">
                <div class="space-x-3">
                    <Button type="secondary" @click="acceptAll">
                        Akceptuj wszystkie
                    </Button>
                    <Button type="ghost" @click="acceptSelected">
                        Zapisz wybrane
                    </Button>
                </div>
                <Button type="ghost" @click="rejectAll">Odrzuć wszystkie</Button>
            </div>
        </template>
    </BaseModal>
</template>