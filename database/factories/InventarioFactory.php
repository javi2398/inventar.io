<?php

namespace Database\Factories;

use App\Models\Almacen;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario>
 */
class InventarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_producto'       => Producto::factory(),
            'id_almacen'        => Almacen::factory(),
            'cantidad_actual'   => $this->faker->numberBetween(1, 200),
            'precio_unitario'   => $this->faker->randomFloat(2, 1, 500),
            'fecha_entrada'     => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'fecha_salida'      => null,
            'fecha_vencimiento' => null,
        ];
    }
}
