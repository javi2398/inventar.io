<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comprador>
 */
class CompradorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'         => $this->faker->name(),
            'email'          => $this->faker->unique()->safeEmail(),
            'identificacion' => $this->faker->unique()->numerify('########X'),
            'telefono'       => $this->faker->phoneNumber(),
            'direccion'      => $this->faker->address(),
            'tipo_comprador' => $this->faker->randomElement(['particular', 'empresa']),
        ];
    }
}
