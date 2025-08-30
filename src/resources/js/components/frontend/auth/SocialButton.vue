<script setup>
import IconGoogle from "@shopen/components/icons/IconGoogle.vue";
import IconFacebook from "@shopen/components/icons/IconFacebook.vue";

const props = defineProps({
    provider: {type: Object}
})

const onClick = () => {
    window.location.href = route('oauth.' + props.provider + '.redirect');
}
</script>

<template>
    <button type="button"
            @click="onClick"
            class="cursor-pointer text-neutral-600 rounded px-2 sm:px-4 py-2.5 leading-normal text:sm sm:text-md
            transition duration-300 ease-in-out hover:shadow focus:shadow focus:outline-none
            focus:ring-0 active:shadow border border-light flex items-center justify-center w-full">
        <span class="flex items-center gap-4" v-if="provider === 'facebook'"><IconFacebook size="xl"/> Kontynuuj przez Facebooka</span>
        <span class="flex items-center gap-4" v-if="provider === 'google'"><IconGoogle size="xl"/> Kontynuuj przez konto Google</span>
    </button>
</template>