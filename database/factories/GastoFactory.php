<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gasto>
 */
class GastoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user'          => User::factory(),
            'concepto'         => $this->faker->sentence(3),
            'precio'           => $this->faker->randomFloat(2, 5, 1000),
            'fecha'            => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'gasto_recurrente' => $this->faker->boolean(),
        ];
    }
}
