@if ($block->isCartEmpty())
    <div class="flex flex-col items-center justify-center">
        <div class="text-neutral-200 mb-4">
            <icon-cart-empty xl></icon-cart-empty>
        </div>
        <div class="mb-10 text-neutral-400 text-xl"> Koszyk jest pusty </div>
    </div>
@else
    <cart :logged-in="{{ Auth::check() ? 'true' : 'false'  }}"></cart>
@endif