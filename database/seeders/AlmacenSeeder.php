<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Almacen;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No se encontró ningún usuario en la base de datos.');
            return;
        }

        $almacenes = [
    ['nombre' => 'Almacén Central',       'direccion' => 'Calle Mayor 1, Madrid'],
    ['nombre' => 'Depósito Norte',        'direccion' => 'Calle Norte 10, Madrid'],
    ['nombre' => 'Sucursal Sur',          'direccion' => 'Avenida Sur 5, Barcelona'],
    ['nombre' => 'Centro Logístico',      'direccion' => 'Polígono Industrial 3, Valencia'],
    ['nombre' => 'Almacén Secundario',    'direccion' => 'Calle Falsa 123, Sevilla'],

    ['nombre' => 'Base Este',             'direccion' => 'Camino del Este 22, Zaragoza'],
    ['nombre' => 'Punto de Reparto Oeste','direccion' => 'Calle del Río 17, A Coruña'],
    ['nombre' => 'Depósito Express',      'direccion' => 'Carretera Nacional 340, Málaga'],
    ['nombre' => 'Sucursal Norte',        'direccion' => 'Paseo del Bosque 8, Bilbao'],
    ['nombre' => 'Plataforma Central 2',  'direccion' => 'Avenida de la Industria 45, Getafe'],

    ['nombre' => 'Almacén Marítimo',      'direccion' => 'Puerto Comercial s/n, Valencia'],
    ['nombre' => 'Mini Almacén Local',    'direccion' => 'Calle Real 12, Salamanca'],
    ['nombre' => 'Nodo Logístico Norte',  'direccion' => 'Parque Empresarial Norte 5, Oviedo'],
    ['nombre' => 'Centro de Distribución','direccion' => 'Ronda Exterior 15, Valladolid'],
    ['nombre' => 'Almacén de Repuestos',  'direccion' => 'Calle Mecánicos 33, Zaragoza'],

    ['nombre' => 'Base Regional Sur',     'direccion' => 'Av. de Andalucía 18, Granada'],
    ['nombre' => 'Depósito Urbano',       'direccion' => 'Calle Comercio 9, Pamplona'],
    ['nombre' => 'Sucursal Levante',      'direccion' => 'Av. Mediterráneo 100, Alicante'],
    ['nombre' => 'Centro Logístico Oeste','direccion' => 'Polígono Oeste 77, Badajoz'],
    ['nombre' => 'Almacén de Temporada',  'direccion' => 'Carretera de Soria km 5, Logroño'],
    ['nombre' => 'Nodo Transitorio',      'direccion' => 'Camino Viejo 14, Cuenca'],
    ['nombre' => 'Depósito Secundario',   'direccion' => 'Calle Proveedores 3, León'],
    ['nombre' => 'Punto Logístico Exprés','direccion' => 'Ronda de Servicio 21, Tarragona'],
    ['nombre' => 'Sucursal Atlántico',    'direccion' => 'Paseo Marítimo 2, Vigo'],
    ['nombre' => 'Centro de Acopio Este', 'direccion' => 'Av. del Trabajo 81, Castellón'],
];

        foreach ($almacenes as $data) {
            Almacen::create([
                'id_user'   => $user->id,
                'nombre'    => $data['nombre'],
                'direccion' => $data['direccion'],
            ]);
        }
    }
}
