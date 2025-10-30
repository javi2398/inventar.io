<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venta;
use App\Models\Almacen;
use App\Models\Producto;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use App\Models\DetalleCompra;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategoriaSeeder::class,
            CompradorSeeder::class,
            ProductoSeeder::class,
            ProveedorSeeder::class,
            AlmacenSeeder::class,
            GastoSeeder::class,
            CompraSeeder::class,
            VentaSeeder::class,
            DetalleCompraSeeder::class,
            DetalleVentaSeeder::class,
            InventarioSeeder::class,
        ]);
    }
}