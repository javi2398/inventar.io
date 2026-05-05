<?php

namespace Database\Factories;

use App\Models\Almacen;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleCompra>
 */
class DetalleCompraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_compra'         => Compra::factory(),
            'id_producto'       => Producto::factory(),
            'id_almacen'        => Almacen::factory(),
            'cantidad_actual'   => $this->faker->numberBetween(1, 100),
            'precio_unitario'   => $this->faker->randomFloat(2, 1, 500),
            'estado'            => $this->faker->boolean(),
            'fecha_vencimiento' => null,
        ];
    }
}
