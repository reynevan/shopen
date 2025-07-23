<?php

namespace Shopen\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Shopen\Core\Support\Product\Price\PriceProcessor;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Repositories\Product\Price\ProductPriceRuleRepository;
use Shopen\Repositories\Product\ProductRepository;

class BatchRecalculateProductsPrices implements ShouldQueue
{
    use Queueable;

    private ProductRepository $productRepository;
    private PriceProcessor $priceProcessor;

    public function __construct(private readonly array $productsIds)
    {
    }

    public function handle(
        ProductPriceRuleRepository $productPriceRuleRepository,
        ProductRepository          $productRepository,
        PriceProcessor           $priceProcessor
    ): void
    {
        $this->productRepository = $productRepository;
        $this->priceProcessor = $priceProcessor;

        $priceRules = $productPriceRuleRepository->getActive();

        foreach ($priceRules as $priceRule) {
            $this->processPriceRule($priceRule);
        }
    }

    protected function processPriceRule(ProductPriceRule $priceRule): void
    {
        $products = $this->productRepository
            ->getQueryMatchingForPriceRule($priceRule)
            ->whereIn('id', $this->productsIds)
            ->get();

        foreach ($products as $product) {
            $this->priceProcessor->processRule($product, $priceRule);
        }
    }

}
