<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Compra;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $proveedores = Proveedor::pluck('id');

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario. Ejecuta primero el UserSeeder.');
            return;
        }

        if ($proveedores->isEmpty()) {
            $this->command->warn('No hay proveedores en la tabla proveedores. Ejecuta primero ProveedorSeeder.');
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            // Fecha aleatoria en los últimos 90 días (~3 meses)
            $fecha = Carbon::now()->subDays(rand(0, 120))->toDateString();

            Compra::create([
                'id_user'      => $user->id,
                'id_proveedor' => $proveedores->random(),
                'fecha_compra' => $fecha,
            ]);
        }
    }
}
