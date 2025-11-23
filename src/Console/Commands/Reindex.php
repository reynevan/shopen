<?php

namespace Shopen\Console\Commands;


use Elastic\Adapter\Indices\Index;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Illuminate\Console\Command;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Category\Category;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Product\ProductAttributeRepository;

class Reindex extends Command
{

    public function __construct(
        protected readonly IndexManager               $indexManager,
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
    protected $description = 'Create indexes and import data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = new Settings();
        $settings->analysis([
            'analyzer' => [
                'autocomplete_analyzer' => [
                    'tokenizer' => 'autocomplete_tokenizer',
                    'filter' => [
                        'lowercase',
                    ],
                ],
            ],
            'tokenizer' => [
                'autocomplete_tokenizer' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 2,
                    'max_gram' => 20,
                    'token_chars' => [
                        'letter',
                        'digit',
                    ],
                ],
            ]
        ]);

        $this->reindexProducts($settings);
        $this->reindexCategories($settings);


        $this->call('scout:import', ['model' => Product::class]);
        $this->call('scout:import', ['model' => Category::class]);
    }

    protected function reindexProducts($settings)
    {
        $indexName = config('scout.prefix') . 'products';
        if ($this->indexManager->exists($indexName)) {
            $this->indexManager->drop($indexName);
        }
        $mapping = new Mapping();
        $mapping->keyword('id');
        $mapping->text('name', [
            'analyzer' => 'polish',
            'boost' => 3,
            'fields' => [
                'autocomplete' => [
                    'type' => 'text',
                    'analyzer' => 'autocomplete_analyzer',
                    'search_analyzer' => 'standard'
                ],
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
        $mapping->keyword('brand_id');
        $mapping->boolean('visible_individually');
        $mapping->flattened('thumbnail', ['index' => false]);
        $mapping->text('searchable_attributes', ['analyzer' => 'polish']);
        $mapping->date('is_new_to');

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
        $this->indexManager->create(new Index($indexName, $mapping, $settings));
    }

    private function reindexCategories(Settings $settings)
    {
        $indexName = config('scout.prefix') . 'categories';
        if ($this->indexManager->exists($indexName)) {
            $this->indexManager->drop($indexName);
        }
        $mapping = new Mapping();
        $mapping->integer('id');
        $mapping->integer('parent_id');
        $mapping->integer('products_count');
        $mapping->keyword('url_key');
        $mapping->text('name', [
            'analyzer' => 'polish',
            'boost' => 3,
            'fields' => [
                'autocomplete' => [
                    'type' => 'text',
                    'analyzer' => 'autocomplete_analyzer',
                    'search_analyzer' => 'standard'
                ],
                'keyword' => [
                    'type' => 'keyword',
                ]
            ]
        ]);

        $this->indexManager->create(new Index($indexName, $mapping, $settings));
    }
}
