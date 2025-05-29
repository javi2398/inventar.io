<?php

namespace Database\Seeders;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Database\Seeder;

class DetalleVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ventas    = Venta::pluck('id');
        $productos = Producto::all();

        if ($ventas->isEmpty()) {
            $this->command->warn('No hay registros en la tabla ventas. Ejecuta primero VentaSeeder.');
            return;
        }

        if ($productos->isEmpty()) {
            $this->command->warn('No hay registros en la tabla productos. Ejecuta primero ProductoSeeder.');
            return;
        }

        foreach ($ventas as $ventaId) {
            // Para cada venta, seleccionamos de 1 a 3 productos aleatorios
            $seleccion = $productos->shuffle()->take(rand(1, 3));

            foreach ($seleccion as $producto) {
                DetalleVenta::create([
                    'id_venta'        => $ventaId,
                    'id_producto'     => $producto->id,
                    'cantidad'        => rand(1, 5),
                    'precio_unitario' => rand(100, 1000) / 100, // 1.00 – 10.00 €
                ]);
            }
        }
    }
}
