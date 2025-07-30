<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    product: {
        type: Object,
        required: true,
        validator: (p) => p.name && p.image,
    },
});

const availabilityMap = {
    InStock: 'https://schema.org/InStock',
    OutOfStock: 'https://schema.org/OutOfStock'
};

const jsonLdScript = computed(() => {
    const data = {
        '@context': 'https://schema.org/',
        '@type': 'Product',
        name: props.product.attributes.name,
        image: props.product.image,
        description: props.product.attributes.description || '',
        sku: props.product.sku || undefined,

        offers: {
            '@type': 'Offer',
            url: props.product.url,
            priceCurrency: 'PLN',
            price: props.product.price.final_price_raw,
            availability: props.product.in_stock ? availabilityMap.InStock : availabilityMap.OutOfStock,
            itemCondition: 'https://schema.org/NewCondition',
        },
    };

    if (props.product.brand) {
        data.brand = {
            '@type': 'Brand',
            name: props.product.brand,
        };
    }

    if (props.product.rating && props.product.reviews_count > 0) {
        data.aggregateRating = {
            '@type': 'AggregateRating',
            ratingValue: props.product.rating,
            reviewCount: props.product.reviews_count,
        };
    }

    return JSON.stringify(data, null, 2);
});
</script>

<template>
    <Head>
        <component is="script" type="application/ld+json">
            {{ jsonLdScript }}
        </component>
    </Head>
</template>