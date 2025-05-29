<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\DetalleCompra;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DetalleCompraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $compras   = Compra::pluck('id');
        $productos = Producto::all(); // Necesitamos los datos, no solo los IDs
        $almacenes = Almacen::pluck('id');

        if ($compras->isEmpty()) {
            $this->command->warn('No hay registros en la tabla compras. Ejecuta primero CompraSeeder.');
            return;
        }

        if ($productos->isEmpty()) {
            $this->command->warn('No hay registros en la tabla productos. Ejecuta primero ProductoSeeder.');
            return;
        }

        if ($almacenes->isEmpty()) {
            $this->command->warn('No hay registros en la tabla almacenes. Ejecuta primero AlmacenSeeder.');
            return;
        }

        foreach ($compras as $compraId) {
            $seleccion = $productos->shuffle()->take(rand(1, 3));

            foreach ($seleccion as $producto) {
                $almacenId = $almacenes->random();

                DetalleCompra::create([
                    'id_compra'         => $compraId,
                    'id_producto'       => $producto->id,
                    'id_almacen'        => $almacenId,
                    'cantidad_actual'   => rand(1, 10),
                    'precio_unitario'   => rand(100, 1000) / 100, // 1.00 – 10.00 €
                    'estado'            => (bool) rand(0, 1), // aleatorio true o false
                    'fecha_vencimiento' => $producto->perecedero
                        ? Carbon::now()->addDays(rand(30, 365))->toDateString()
                        : null,
                ]);
            }
        }
    }
}
