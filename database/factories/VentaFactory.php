<?php

namespace Database\Factories;

use App\Models\Comprador;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user'      => User::factory(),
            'id_comprador' => Comprador::factory(),
            'fecha_venta'  => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ];
    }
}
