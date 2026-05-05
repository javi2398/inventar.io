<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleVenta>
 */
class DetalleVentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_producto'     => Producto::factory(),
            'id_venta'        => Venta::factory(),
            'cantidad'        => $this->faker->numberBetween(1, 20),
            'precio_unitario' => $this->faker->randomFloat(2, 1, 500),
        ];
    }
}
