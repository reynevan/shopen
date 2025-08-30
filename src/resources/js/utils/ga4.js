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
    gtag && gtag('event', 'search', {
        search_term: searchTerm,
        ...(resultsCount !== null && { search_results: resultsCount })
    });
};

// ===== EVENTY PRODUKTÓW =====

export const trackViewItemList = (items, listName = 'Product List') => {
    gtag && gtag('event', 'view_item_list', {
        item_list_name: listName,
        items: items.map((item, index) => ({
            item_id: item.id || item.sku,
            item_name: item.name,
            item_category: item.category,
            item_brand: item.brand || '',
            price: item.price,
            index: index,
            quantity: item.quantity || 1
        }))
    });
};

export const trackViewItem = (product, category = null) => {
    gtag && gtag('event', 'view_item', {
        currency: 'PLN',
        value: product.final_price || product.price,
        items: [{
            item_id: product.id || product.sku,
            item_name: product.name,
            item_category: category,
            item_brand: product.brand || '',
            price: product.final_price || product.price,
            quantity: 1
        }]
    });
};

export const trackSelectItem = (product, listName = 'Product List', index = 0) => {
    gtag && gtag('event', 'select_item', {
        item_list_name: listName,
        items: [{
            item_id: product.id || product.sku,
            item_name: product.name,
            item_category: product.category,
            price: product.final_price || product.price,
            index: index
        }]
    });
};

// ===== EVENTY KOSZYKA =====

export const trackAddToCart = (product, quantity = 1) => {
    gtag && gtag('event', 'add_to_cart', {
        currency: 'PLN',
        value: (product.final_price || product.price) * quantity,
        items: [{
            item_id: product.id || product.sku,
            item_name: product.name,
            item_category: product.category,
            item_brand: product.brand || '',
            price: product.final_price || product.price,
            quantity: quantity
        }]
    });
};

export const trackRemoveFromCart = (product, quantity = 1) => {
    gtag && gtag('event', 'remove_from_cart', {
        currency: 'PLN',
        value: (product.final_price || product.price) * quantity,
        items: [{
            item_id: product.id || product.sku,
            item_name: product.name,
            item_category: product.category,
            price: product.final_price || product.price,
            quantity: quantity
        }]
    });
};

export const trackViewCart = (cartItems) => {
    const totalValue = cartItems.reduce((sum, item) => sum + (item.final_price * item.quantity), 0);

    gtag && gtag('event', 'view_cart', {
        currency: 'PLN',
        value: totalValue,
        items: cartItems.map(item => ({
            item_id: item.product_id || item.id,
            item_name: item.name,
            item_category: item.category,
            price: item.final_price || item.price,
            quantity: item.quantity
        }))
    });
};

// ===== EVENTY CHECKOUT =====

export const trackBeginCheckout = (cartItems, couponCode = null) => {
    const totalValue = cartItems.reduce((sum, item) => sum + (item.final_price * item.quantity), 0);

    gtag && gtag('event', 'begin_checkout', {
        currency: 'PLN',
        value: totalValue,
        ...(couponCode && { coupon: couponCode }),
        items: cartItems.map(item => ({
            item_id: item.product_id || item.id,
            item_name: item.name,
            item_category: item.category,
            price: item.final_price || item.price,
            quantity: item.quantity
        }))
    });
};

export const trackAddShippingInfo = (cartItems, shippingMethod) => {
    const totalValue = cartItems.reduce((sum, item) => sum + (item.final_price * item.quantity), 0);

    gtag && gtag('event', 'add_shipping_info', {
        currency: 'PLN',
        value: totalValue,
        shipping_tier: shippingMethod,
        items: cartItems.map(item => ({
            item_id: item.product_id || item.id,
            item_name: item.name,
            price: item.final_price || item.price,
            quantity: item.quantity
        }))
    });
};

export const trackAddPaymentInfo = (cartItems, paymentMethod) => {
    const totalValue = cartItems.reduce((sum, item) => sum + (item.final_price * item.quantity), 0);

    gtag && gtag('event', 'add_payment_info', {
        currency: 'PLN',
        value: totalValue,
        payment_type: paymentMethod,
        items: cartItems.map(item => ({
            item_id: item.product_id || item.id,
            item_name: item.name,
            price: item.final_price || item.price,
            quantity: item.quantity
        }))
    });
};

// ===== EVENT ZAKUPU =====

export const trackPurchase = (order) => {
    gtag && gtag('event', 'purchase', {
        transaction_id: order.order_number || order.uuid,
        value: order.total_amount,
        currency: 'PLN',
        shipping: order.shipping_amount || 0,
        tax: 0, // jeśli masz podatek osobno
        ...(order.promo_code && { coupon: order.promo_code }),
        items: order.items.map(item => ({
            item_id: item.product_id || item.sku,
            item_name: item.name,
            item_category: item.category,
            price: item.final_price,
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
        value: product.final_price || product.price,
        items: [{
            item_id: product.id || product.sku,
            item_name: product.name,
            item_category: product.category,
            price: product.final_price || product.price,
            quantity: 1
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
    gtag && gtag('event', 'login', {
        method: method
    });
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