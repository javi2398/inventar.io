<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comprador;

class CompradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $compradores = [
            // Particulares
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juanperez@gmail.com',
                'identificacion' => '12345678A',
                'telefono' => '600111111',
                'direccion' => 'Calle Falsa 123, Madrid',
                'tipo_comprador' => 'particular',
            ],
            [
                'nombre' => 'Lucía Gómez',
                'email' => 'luciagomez@gmail.com',
                'identificacion' => '87654321B',
                'telefono' => '600222222',
                'direccion' => 'Avenida Sol 45, Sevilla',
                'tipo_comprador' => 'particular',
            ],
            [
                'nombre' => 'Carlos Ruiz',
                'email' => 'carlosruiz@gmail.com',
                'identificacion' => '11223344C',
                'telefono' => '600333333',
                'direccion' => 'Calle Luna 10, Valencia',
                'tipo_comprador' => 'particular',
            ],
            [
                'nombre' => 'Marta Sánchez',
                'email' => 'martasanchez@gmail.com',
                'identificacion' => '44332211D',
                'telefono' => '600444444',
                'direccion' => 'Calle Mar 8, Málaga',
                'tipo_comprador' => 'particular',
            ],
            [
                'nombre' => 'Pedro López',
                'email' => 'pedrolopez@gmail.com',
                'identificacion' => '55667788E',
                'telefono' => '600555555',
                'direccion' => 'Calle Río 20, Granada',
                'tipo_comprador' => 'particular',
            ],

            // Empresas
            [
                'nombre' => 'Tech Solutions S.L.',
                'email' => 'contacto@techsolutions.com',
                'identificacion' => 'B12345678',
                'telefono' => '911111111',
                'direccion' => 'Calle Tecnología 1, Madrid',
                'tipo_comprador' => 'empresa',
            ],
            [
                'nombre' => 'Logística Express',
                'email' => 'info@logisticaexpress.com',
                'identificacion' => 'B87654321',
                'telefono' => '922222222',
                'direccion' => 'Avenida Transporte 5, Zaragoza',
                'tipo_comprador' => 'empresa',
            ],
            [
                'nombre' => 'Distribuciones AlSur',
                'email' => 'ventas@alsur.com',
                'identificacion' => 'B11223344',
                'telefono' => '933333333',
                'direccion' => 'Calle Comercio 99, Córdoba',
                'tipo_comprador' => 'empresa',
            ],
            [
                'nombre' => 'Servicios Delta',
                'email' => 'delta@servicios.com',
                'identificacion' => 'B44332211',
                'telefono' => '944444444',
                'direccion' => 'Calle Progreso 2, Bilbao',
                'tipo_comprador' => 'empresa',
            ],
            [
                'nombre' => 'Mercados Unidos',
                'email' => 'contacto@mercadosunidos.com',
                'identificacion' => 'B55667788',
                'telefono' => '955555555',
                'direccion' => 'Avenida Central 7, Alicante',
                'tipo_comprador' => 'empresa',
            ],
        ];

        foreach ($compradores as $data) {
            Comprador::create([
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'identificacion' => $data['identificacion'],
                'telefono' => $data['telefono'],
                'direccion' => $data['direccion'],
                'tipo_comprador' => $data['tipo_comprador'],
            ]);
        }
    }
}
