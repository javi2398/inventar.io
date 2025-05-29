<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapa de categorías por nombre => id
        $categorias = Categoria::pluck('id', 'nombre');

        if ($categorias->isEmpty()) {
            $this->command->warn('No hay categorías disponibles. Ejecuta primero el CategoriaSeeder.');
            return;
        }

        $productos = [
            [
                'categoria' => 'Frutas y Verduras',
                'codigo' => 'FRU001',
                'nombre' => 'Manzana',
                'descripcion' => 'Manzana roja de temporada',
                'perecedero' => true,
            ],
            [
                'categoria' => 'Carnes y Pescados',
                'codigo' => 'CAR001',
                'nombre' => 'Filete de ternera',
                'descripcion' => 'Filete fresco para asar',
                'perecedero' => true,
            ],
            [
                'categoria' => 'Lácteos',
                'codigo' => 'LAC001',
                'nombre' => 'Yogur natural',
                'descripcion' => 'Yogur sin azúcar añadido',
                'perecedero' => true,
            ],
            [
                'categoria' => 'Panadería',
                'codigo' => 'PAN001',
                'nombre' => 'Pan de centeno',
                'descripcion' => 'Pan integral de centeno',
                'perecedero' => true,
            ],
            [
                'categoria' => 'Bebidas',
                'codigo' => 'BEB001',
                'nombre' => 'Zumo de naranja',
                'descripcion' => 'Zumo 100% natural sin conservantes',
                'perecedero' => true,
            ],
            [
                'categoria' => 'Higiene personal',
                'codigo' => 'HIG001',
                'nombre' => 'Champú',
                'descripcion' => 'Champú anticaspa para uso frecuente',
                'perecedero' => false,
            ],
            [
                'categoria' => 'Limpieza del hogar',
                'codigo' => 'LIM001',
                'nombre' => 'Lejía',
                'descripcion' => 'Desinfectante para uso doméstico',
                'perecedero' => false,
            ],
            [
                'categoria' => 'Electrodomésticos',
                'codigo' => 'ELE001',
                'nombre' => 'Hervidor de agua',
                'descripcion' => 'Hervidor eléctrico de 1.5 litros',
                'perecedero' => false,
            ],
            [
                'categoria' => 'Ropa',
                'codigo' => 'ROP001',
                'nombre' => 'Sudadera con capucha',
                'descripcion' => 'Sudadera de algodón unisex',
                'perecedero' => false,
            ],
            [
                'categoria' => 'Papelería',
                'codigo' => 'PAP001',
                'nombre' => 'Lápiz HB',
                'descripcion' => 'Lápiz clásico para escritura o dibujo',
                'perecedero' => false,
            ],
        ];

        $ImagenDeMentira = 'https://chitoroshop.com/cdn/shop/files/Figurine-Pikachu-Moncolle-MS-01-ChitoroShop-2822.png?v=1710390192';

        foreach ($productos as $item) {
            $categoriaId = $categorias[$item['categoria']] ?? null;

            if (!$categoriaId) {
                $this->command->warn("Categoría no encontrada: {$item['categoria']}");
                continue;
            }

            Producto::create([
                'id_categoria' => $categoriaId,
                'codigo'       => $item['codigo'],
                'nombre'       => $item['nombre'],
                'descripcion'  => $item['descripcion'],
                'perecedero'   => $item['perecedero'],
                'imagen'       => $ImagenDeMentira,
            ]);
        }
    }
}
