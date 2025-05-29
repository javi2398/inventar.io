<?php

namespace Database\Seeders;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Almacen;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = Producto::all();
        $almacenes = Almacen::pluck('id');

        if ($productos->isEmpty()) {
            $this->command->warn('No hay productos. Ejecuta primero ProductoSeeder.');
            return;
        }

        if ($almacenes->isEmpty()) {
            $this->command->warn('No hay almacenes. Ejecuta primero AlmacenSeeder.');
            return;
        }

        foreach ($productos as $producto) {
            // Relacionar con 1 o 2 almacenes al azar
            $almacenesAleatorios = $almacenes->shuffle()->take(rand(1, 2));

            foreach ($almacenesAleatorios as $almacenId) {
                Inventario::create([
                    'id_producto'       => $producto->id,
                    'id_almacen'        => $almacenId,
                    'cantidad_actual'   => rand(5, 50),
                    'precio_unitario'   => rand(100, 1000) / 100, // 1.00 - 10.00 €
                    'fecha_entrada'     => Carbon::now()->subDays(rand(5, 60))->toDateString(),
                    'fecha_salida'      => rand(0, 1) ? Carbon::now()->subDays(rand(1, 4))->toDateString() : null,
                    'fecha_vencimiento' => $producto->perecedero
                        ? Carbon::now()->addDays(rand(30, 180))->toDateString()
                        : null,
                ]);
            }
        }
    }
}
