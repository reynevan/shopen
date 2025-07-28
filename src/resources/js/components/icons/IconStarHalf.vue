<script setup>
    const props = defineProps({
        sm: {
            type: Boolean,
            default: false
        },
        lg: {
            type: Boolean,
            default: false
        },
        md: {
            type: Boolean,
            default: false
        }
    })

    const size = props.lg ? 32 : props.md ? 24 : props.sm ? 12 : 16;
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" viewBox="0 0 16 16">
        <defs>
            <clipPath id="star-left-half">
                <rect x="0" y="0" width="8" height="16" />
            </clipPath>
            <clipPath id="star-right-half">
                <rect x="8" y="0" width="8" height="16" />
            </clipPath>
        </defs>

        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"
              fill="var(--color-gray-300)"
              clip-path="url(#star-right-half)" />

        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"
              fill="currentColor"
              clip-path="url(#star-left-half)" />
    </svg>
</template>