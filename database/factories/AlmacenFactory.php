<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Almacen>
 */
class AlmacenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user'   => User::factory(),
            'nombre'    => 'Almacén ' . $this->faker->unique()->city(),
            'direccion' => $this->faker->address(),
        ];
    }
}
