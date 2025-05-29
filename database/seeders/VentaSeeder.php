<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Venta;
use App\Models\Comprador;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $compradores = Comprador::pluck('id');

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario. Ejecuta primero el UserSeeder.');
            return;
        }

        if ($compradores->isEmpty()) {
            $this->command->warn('No hay compradores en la tabla compradores. Ejecuta primero CompradorSeeder.');
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $fecha = Carbon::now()->subDays(rand(0, 120))->toDateString();

            Venta::create([
                'id_user'      => $user->id,
                'id_comprador' => $compradores->random(),
                'fecha_venta'  => $fecha,
            ]);
        }
    }
}
