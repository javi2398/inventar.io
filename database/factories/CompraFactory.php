<?php

namespace Database\Factories;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compra>
 */
class CompraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user'      => User::factory(),
            'id_proveedor' => Proveedor::factory(),
            'fecha_compra' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ];
    }
}
