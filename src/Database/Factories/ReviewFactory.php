<?php

namespace Shopen\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shopen\Enums\Product\Review\ReviewStatus;
use Shopen\Models\Product\Review\ProductReview;


class ReviewFactory extends Factory
{

    protected $model = ProductReview::class;


    public function definition(): array
    {
        return [
            'rating' => $this->faker->numberBetween(0, 5),
            'comment' => $this->faker->realText(),
            'status' => $this->faker->randomElement(ReviewStatus::values()),
            'is_verified_purchase' => $this->faker->boolean(),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}
