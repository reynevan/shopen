atrybuty
@foreach ($block->getAttributesList() as $attribute)
    <div>
        {{ $attribute->name }}: {{ $product->getAttribute($attribute->code) }}
    </div>
@endforeach