<div>
    <stock-status :status="{{ $block->isInStock() ? 'true': 'false' }}" :configurable="{{ $block->getProduct()->isConfigurable() ? 'true' : 'false' }}">
</div>
