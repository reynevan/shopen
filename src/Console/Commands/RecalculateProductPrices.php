<?php

namespace Shopen\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductRepository;

class RecalculateProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:recalculate-product-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate product prices using special prices and price rules';

    public function __construct(private readonly ProductRepository $productRepository)
    {
        parent::__construct();
    }

    public function handle()
    {
        $products = $this->productRepository->getAll();

    }
}
