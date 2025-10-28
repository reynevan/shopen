// resources/js/utils/ga4.js


// ===== PODSTAWOWE EVENTY =====

export const trackPageView = (pageTitle = null, pagePath = null) => {
    gtag && gtag('event', 'page_view', {
        page_title: pageTitle || document.title,
        page_path: pagePath || window.location.pathname,
        page_location: window.location.href,
    });
};

export const trackSearch = (searchTerm, resultsCount = null) => {
    dataLayer && dataLayer.push({
        event: 'search', search_term: searchTerm,
        ...(resultsCount !== null && { search_results: resultsCount})
        });
};

// ===== EVENTY PRODUKTÓW =====

export const trackViewItemList = (items, listName, listId) => {
    gtag && gtag('event', 'view_item_list', {
        item_list_name: listName,
        item_list_id: listId,
        items: items.map((item, index) => ({
            item_id: item.sku,
            item_name: item.attributes.name,
            item_brand: item.brand?.name ?? null,
            price: parseFloat(item.price.final_price_raw),
            index: index
        }))
    });
};

function getActiveVariant(variants) {
    const selectedValues = variants
        .map(variant => {
            const selectedProduct = variant.products.find(product => product.is_selected === true);
            return selectedProduct ? selectedProduct.attribute_value : null;
        })
        .filter(value => value !== null);

    return selectedValues.join('|');
}

export const trackViewItem = (product, variants) => {
    gtag && gtag('event', 'view_item', {
        currency: 'PLN',
        value: product.price.final_price_raw,
        items: [{
            item_id: product.sku,
            item_name: product.attributes.name,
            item_variant: variants && variants.length ? getActiveVariant(variants) : null,
            item_brand: product.brand?.name || '',
            price: parseFloat(product.price.final_price_raw),
        }]
    });
};

export const trackSelectItem = (product, listName) => {
    gtag && gtag('event', 'select_item', {
        item_list_name: listName,
        items: [{
            item_id: product.sku,
            item_name: product.attributes.name,
            price: parseFloat(product.price.final_price_raw),
            item_brand: product.brand?.name ?? null
        }]
    });
};

// ===== EVENTY KOSZYKA =====

export const trackAddToCart = (product, quantity = 1) => {
    gtag && gtag('event', 'add_to_cart', {
        currency: 'PLN',
        value: (product.price.final_price_raw) * quantity,
        items: [{
            item_id: product.sku,
            item_name: product.name ?? product.attributes.name,
            item_brand: product.brand?.name || null,
            price: parseFloat(product.price.final_price_raw),
            quantity: quantity
        }]
    });
};

export const trackRemoveFromCart = (item, quantity = 1) => {
    gtag && gtag('event', 'remove_from_cart', {
        currency: 'PLN',
        value: item.total_final_price_raw,
        items: [{
            item_id: item.product.sku,
            item_name: item.product.name,
            price: parseFloat(item.final_price_raw),
            quantity: quantity
        }]
    });
};

export const trackViewCart = (cartItems) => {
    const totalValue = cartItems.reduce((sum, item) => sum + (item.total_final_price_raw), 0);

    gtag && gtag('event', 'view_cart', {
        currency: 'PLN',
        value: totalValue,
        items: cartItems.map(item => ({
            item_id: item.product.sku,
            item_name: item.product.name,
            price: parseFloat(item.total_final_price_raw),
            quantity: item.quantity
        }))
    });
};

// ===== EVENTY CHECKOUT =====

export const trackBeginCheckout = (cartItems) => {
    const totalValue = cartItems.reduce((sum, item) => sum + item.total_final_price_raw, 0);

    gtag && gtag('event', 'begin_checkout', {
        currency: 'PLN',
        value: totalValue,
        items: cartItems.map(item => ({
            item_id: item.product.sku,
            item_name: item.product.name,
            price: parseFloat(item.total_final_price_raw),
            quantity: item.quantity
        }))
    });
};

export const trackAddShippingInfo = (cartItems, shippingMethod) => {
    const totalValue = cartItems.reduce((sum, item) => sum + item.total_final_price_raw, 0);

    gtag && gtag('event', 'add_shipping_info', {
        currency: 'PLN',
        value: totalValue,
        shipping_tier: shippingMethod,
        items: cartItems.map(item => ({
            item_id: item.product.sku,
            item_name: item.product.name,
            price: parseFloat(item.total_final_price_raw),
            quantity: item.quantity
        }))
    });
};

export const trackAddPaymentInfo = (cartItems, paymentMethod) => {
    const totalValue = cartItems.reduce((sum, item) => sum + item.total_final_price_raw, 0);

    gtag && gtag('event', 'add_payment_info', {
        currency: 'PLN',
        value: totalValue,
        payment_type: paymentMethod,
        items: cartItems.map(item => ({
            item_id: item.product.sku,
            item_name: item.product.name,
            price: parseFloat(item.total_final_price_raw),
            quantity: item.quantity
        }))
    });
};

// ===== EVENT ZAKUPU =====

export const trackPurchase = (order) => {
    gtag && gtag('event', 'purchase', {
        transaction_id: order.order_number,
        value: order.total_amount,
        currency: 'PLN',
        shipping: order.shipping_amount_raw || 0,
        tax: order.tax_amount_raw,
        items: order.items.map(item => ({
            item_id: item.sku,
            item_name: item.name,
            price: parseFloat(item.total_raw),
            quantity: item.quantity
        }))
    });
};

// ===== EVENTY REFUND =====

export const trackRefund = (order, refundedItems = null) => {
    const eventData = {
        transaction_id: order.order_number || order.uuid,
        currency: 'PLN'
    };

    if (refundedItems) {
        // Częściowy zwrot
        eventData.value = refundedItems.reduce((sum, item) => sum + (item.final_price * item.quantity), 0);
        eventData.items = refundedItems.map(item => ({
            item_id: item.product_id || item.sku,
            item_name: item.name,
            price: item.final_price,
            quantity: item.quantity
        }));
    } else {
        // Pełny zwrot
        eventData.value = order.total_amount;
    }

    gtag && gtag('event', 'refund', eventData);
};

// ===== EVENTY PROMOCJI =====

export const trackViewPromotion = (promoCode) => {
    gtag && gtag('event', 'view_promotion', {
        promotion_id: promoCode.code,
        promotion_name: promoCode.name,
        creative_name: promoCode.description,
        location_id: 'banner' // lub inne miejsce wyświetlenia
    });
};

export const trackSelectPromotion = (promoCode) => {
    gtag && gtag('event', 'select_promotion', {
        promotion_id: promoCode.code,
        promotion_name: promoCode.name,
        creative_name: promoCode.description,
        location_id: 'banner'
    });
};

// ===== EVENTY WISHLIST =====

export const trackAddToWishlist = (product) => {
    gtag && gtag('event', 'add_to_wishlist', {
        currency: 'PLN',
        value: parseFloat(product.price.final_price_raw),
        items: [{
            item_id: product.sku,
            item_name: product.attributes.name,
            price: parseFloat(product.price.final_price_raw)
        }]
    });
};

// ===== EVENTY UŻYTKOWNIKÓW =====

export const trackSignUp = (method = 'email') => {
    gtag && gtag('event', 'sign_up', {
        method: method
    });
};

export const trackLogin = (method = 'email') => {
    dataLayer && dataLayer.push({event: 'login', method: method});
};

// ===== EVENTY RECENZJI =====

export const trackReviewSubmit = (product, rating) => {
    gtag && gtag('event', 'review_submit', {
        item_id: product.id || product.sku,
        item_name: product.name,
        rating: rating,
        custom_parameters: {
            review_type: 'product_review'
        }
    });
};

// ===== EVENTY SHARE =====

export const trackShare = (contentType, itemId, method = 'unknown') => {
    gtag && gtag('event', 'share', {
        method: method,
        content_type: contentType,
        item_id: itemId
    });
};

// ===== EVENTY CUSTOM =====

export const trackNewsletterSignup = (location = 'footer') => {
    gtag && gtag('event', 'newsletter_signup', {
        location: location,
        custom_parameters: {
            engagement_type: 'newsletter'
        }
    });
};

export const trackContactForm = (formType = 'contact') => {
    gtag && gtag('event', 'contact_form_submit', {
        form_type: formType,
        custom_parameters: {
            engagement_type: 'contact'
        }
    });
};

export const trackFilterUse = (filterType, filterValue) => {
    gtag && gtag('event', 'filter_products', {
        filter_type: filterType,
        filter_value: filterValue,
        custom_parameters: {
            interaction_type: 'filter'
        }
    });
};

// ===== HELPER DO DEBUGOWANIA =====

export const enableGA4Debug = () => {
    gtag && gtag('config', 'G-XXXXXXXXXX', {
        debug_mode: true
    });
    console.log('GA4 Debug mode enabled');
};