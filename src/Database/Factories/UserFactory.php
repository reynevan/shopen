<?php

namespace Shopen\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Shopen\Models\User;


class UserFactory extends Factory
{

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'role' => User::ROLE_USER,
            'password' => Hash::make('password')
        ];
    }
}
