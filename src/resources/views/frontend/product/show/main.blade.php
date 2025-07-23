@extends('shopen::frontend.layouts.main')

@section('content')


    <div class="flex flex-col sm:flex-row py-10 px-6">
        <div class="mr-0 sm:mr-6">
            @block('product.show.gallery')
        </div>
        <div>
            @block('product.show.name')
            @block('product.show.stock-status')
            @block('product.show.price')
            @block('product.show.variant-select')
            <add-to-cart-button :product-id="{{ $product->id }}"></add-to-cart-button>
            <div>
                @block('product.show.attributes')
            </div>
        </div>
    </div>
    {!! $product->description !!}

@endsection