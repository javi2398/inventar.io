<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proveedor>
 */
class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'   => $this->faker->company(),
            'email'    => $this->faker->unique()->companyEmail(),
            'telefono' => $this->faker->phoneNumber(),
        ];
    }
}
