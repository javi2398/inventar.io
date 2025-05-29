<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Gasto;
use Illuminate\Database\Seeder;

class GastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario. Ejecuta primero el UserSeeder.');
            return;
        }

        $gastos = [
            [
                'concepto' => 'Compra de material de oficina',
                'precio' => 45.75,
                'fecha' => Carbon::now()->subDays(8)->toDate(),
                'gasto_recurrente' => false,
            ],
            [
                'concepto' => 'Pago electricidad',
                'precio' => 120.50,
                'fecha' => Carbon::now()->subMonth()->toDate(),
                'gasto_recurrente' => true,
            ],
            [
                'concepto' => 'Suscripción Internet',
                'precio' => 30.00,
                'fecha' => Carbon::now()->subDays(5)->toDate(),
                'gasto_recurrente' => true,
            ],
            [
                'concepto' => 'Mantenimiento de software',
                'precio' => 200.00,
                'fecha' => Carbon::now()->subDays(15)->toDate(),
                'gasto_recurrente' => false,
            ],
            [
                'concepto' => 'Compra de mobiliario',
                'precio' => 540.00,
                'fecha' => Carbon::now()->subWeeks(3)->toDate(),
                'gasto_recurrente' => false,
            ],
            [
                'concepto' => 'Pago alquiler oficina',
                'precio' => 800.00,
                'fecha' => Carbon::now()->startOfMonth()->toDate(),
                'gasto_recurrente' => true,
            ],
            [
                'concepto' => 'Renovación de dominios',
                'precio' => 95.00,
                'fecha' => Carbon::now()->addDays(2)->toDate(),
                'gasto_recurrente' => false,
            ],
            [
                'concepto' => 'Gasolina vehículo empresa',
                'precio' => 60.25,
                'fecha' => Carbon::now()->subDays(3)->toDate(),
                'gasto_recurrente' => true,
            ],
            [
                'concepto' => 'Servicio limpieza',
                'precio' => 150.00,
                'fecha' => Carbon::now()->addWeek()->toDate(),
                'gasto_recurrente' => true,
            ],
            [
                'concepto' => 'Material promocional',
                'precio' => 210.40,
                'fecha' => Carbon::now()->subDays(10)->toDate(),
                'gasto_recurrente' => false,
            ],
        ];

        foreach ($gastos as $data) {
            Gasto::create([
                'id_user' => $user->id,
                'concepto' => $data['concepto'],
                'precio' => $data['precio'],
                'fecha' => $data['fecha'],
                'gasto_recurrente' => $data['gasto_recurrente'],
            ]);
        }
    }
}
