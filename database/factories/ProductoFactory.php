<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_categoria' => Categoria::factory(),
            'codigo'       => $this->faker->unique()->bothify('PRD-####??'),
            'nombre'       => $this->faker->words(2, true),
            'descripcion'  => $this->faker->sentence(),
            'perecedero'   => $this->faker->boolean(),
            'imagen'       => null,
        ];
    }
}
