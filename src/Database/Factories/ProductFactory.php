<?php

namespace Shopen\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Shopen\Models\Product\Product;


class ProductFactory extends Factory
{

    protected $model = Product::class;


    public function definition(): array
    {
        $qty = rand(0, 20) < 2 ? 0 : rand(0, 100);
        return [
            'sku' => Str::slug(fake()->words(rand(1,3), true)),
            'stock_qty' => $qty,
            'uses_stock' => fake()->boolean(),
            'type' => 'simple',
        ];
    }

    public function configurable(): Factory
    {
        return $this->state([
            'type' => 'configurable',
        ]);
    }

    public function childOf(Product $parent): Factory
    {
        return $this->state([
            'parent_id' => $parent->id,
            'type' => 'simple',
        ]);
    }
}
