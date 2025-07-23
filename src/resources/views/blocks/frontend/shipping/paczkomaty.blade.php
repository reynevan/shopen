@push('head')
    <link rel="stylesheet" href="https://geowidget.inpost.pl/inpost-geowidget.css"/>
@endpush


<checkout-shipping-method-paczkomaty :method="{{ json_encode($block->getShippingMethod()) }}" token="{{ $block->getGeoToken() }}">
</checkout-shipping-method-paczkomaty>