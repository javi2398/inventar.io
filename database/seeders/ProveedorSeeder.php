<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'nombre'  => 'Distribuciones López',
                'email'   => 'contacto@dlopez.es',
                'telefono' => '912345678',
            ],
            [
                'nombre'  => 'Insumos Alimenticios S.A.',
                'email'   => 'ventas@insumosalimenta.com',
                'telefono' => '918765432',
            ],
            [
                'nombre'  => 'Calzados Martínez',
                'email'   => 'info@calzmartinez.es',
                'telefono' => '916543210',
            ],
            [
                'nombre'  => 'Grupo Textil Rivera',
                'email'   => 'info@gruporivera.com',
                'telefono' => '915678901',
            ],
            [
                'nombre'  => 'Lácteos del Sur',
                'email'   => 'pedidos@lacteossur.es',
                'telefono' => '914321789',
            ],
            [
                'nombre'  => 'Electrodomésticos Europa',
                'email'   => 'soporte@electroeuro.com',
                'telefono' => '913210987',
            ],
            [
                'nombre'  => 'Higiene Global',
                'email'   => 'contacto@higieneglobal.net',
                'telefono' => '919876543',
            ],
            [
                'nombre'  => 'Alimentos Naturales',
                'email'   => 'ventas@alimentosnaturales.com',
                'telefono' => '911234567',
            ],
            [
                'nombre'  => 'Suministros del Norte',
                'email'   => 'info@sumnorte.com',
                'telefono' => '917654321',
            ],
            [
                'nombre'  => 'Papelería Moderna',
                'email'   => 'pedidos@papeleriamoderna.com',
                'telefono' => '910123456',
            ],
        ];

        foreach ($proveedores as $data) {
            Proveedor::create($data);
        }
    }
}
