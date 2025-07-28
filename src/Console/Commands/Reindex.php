<?php

namespace Shopen\Console\Commands;


use Elastic\Adapter\Indices\Index;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Indices\Mapping;
use Illuminate\Console\Command;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;

class Reindex extends Command
{

    public function __construct (
        protected readonly IndexManager $indexManager,
        protected readonly ProductAttributeRepository $productAttributeRepository,
    )
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $indexName = config('scout.prefix').'products';
        if ($this->indexManager->exists($indexName)) {
            $this->indexManager->drop($indexName);
        }
        $mapping = new Mapping();
        $mapping->integer('id');
        $mapping->text('name', [
            'analyzer' => 'polish',
            'boost' => 3,
            'fields' => [
                'keyword' => [
                    'type' => 'keyword',
                ]
            ]
        ]);
        $mapping->text('description', ['analyzer' => 'polish']);
        $mapping->text('sku');
        $mapping->float('price');
        $mapping->boolean('in_stock');
        $mapping->integer('stock_qty');
        $mapping->integer('popularity');
        $mapping->float('rating');
        $mapping->integer('reviews_count');
        $mapping->keyword('category_id');
        $mapping->keyword('thumbnail_url', ['index' => false]);
        $mapping->keyword('mobile_thumbnail_url', ['index' => false]);
        $mapping->text('searchable_attributes', ['analyzer' => 'polish']);

        $mapping->flattened('list_attributes', ['index' => false]);

        $attributes = $this->productAttributeRepository->getIndexable();
        foreach ($attributes as $attribute) {
            if ($attribute->backend_type === 'bool') {
                $mapping->boolean($attribute->code);
            } elseif ($attribute->backend_type === 'int') {
                $mapping->integer($attribute->code);
            } elseif ($attribute->backend_type === 'decimal') {
                $mapping->double($attribute->code);
            } elseif ($attribute->backend_type === 'string') {
                $mapping->keyword($attribute->code);
            } elseif ($attribute->backend_type === 'date') {
                $mapping->date($attribute->code);
            } elseif ($attribute->backend_type === 'text') {
                $mapping->text($attribute->code);
            }
        }
        $this->indexManager->create(new Index($indexName, $mapping));


        $this->call('scout:import', ['model' => Product::class]);
    }
}
