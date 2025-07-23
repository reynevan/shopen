<div>
    @if ($block->getTitle())
        <div class="text-xl font-semibold">{{ $block->getTitle() }}</div>
    @endif
    <products-carousel>
        @foreach($block->getProducts() as $product)
            <product-thumbnail :product="{{ json_encode($product) }}" :key="{{ $product->id }}"></product-thumbnail>
        @endforeach
    </products-carousel>
</div>