<?php

namespace Shopen\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Shopen\Models\Attribute\Attribute;


class AttributeFactory extends Factory
{

    protected $model = Attribute::class;


    public function definition(): array
    {
        $name = fake()->unique()->words(rand(1, 3), true);
        $frontendType = fake()->randomElement(['text', 'textarea', 'number', 'select', 'multiselect', 'date', 'price', 'bool']);
        if (in_array($frontendType, ['number', 'select', 'multiselect'])) {
            $backendType = 'int';
        } elseif ($frontendType === 'text') {
            $backendType = 'string';
        } elseif ($frontendType === 'textarea') {
            $backendType = 'text';
        } elseif ($frontendType === 'date') {
            $backendType = 'date';
        } elseif ($frontendType === 'price') {
            $backendType = 'decimal';
        } elseif ($frontendType === 'bool') {
            $backendType = 'bool';
        }
        $units = null;
        if ($frontendType === 'number' && rand(0, 100) > 80) {
            $units = fake()->randomElement(['kg', 'm', 'cm', 'g', 'm^2', 'mb']);
        }
        return [
            'name' => $name,
            'code' => Str::slug($name, '_'),
            'is_filterable' => fake()->boolean(),
            'is_sortable' => fake()->boolean(),
            'is_searchable' => fake()->boolean(),
            'is_system' => fake()->boolean(),
            'is_required' => fake()->boolean(),
            'is_visible_in_details' => fake()->boolean(),
            'is_used_in_list' => fake()->boolean(),
            'backend_type' => $backendType,
            'frontend_type' => $frontendType,
            'units' => $units,
            'sort_order' => rand(1, 1000),
        ];
    }
}
