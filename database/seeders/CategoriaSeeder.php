<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
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

        $categorias = [
            ['nombre' => 'Frutas y Verduras'],
            ['nombre' => 'Carnes y Pescados'],
            ['nombre' => 'Lácteos'],          
            ['nombre' => 'Panadería'],        
            ['nombre' => 'Bebidas'],             
            ['nombre' => 'Higiene personal'],    
            ['nombre' => 'Limpieza del hogar'],  
            ['nombre' => 'Electrodomésticos'],   
            ['nombre' => 'Ropa'],                
            ['nombre' => 'Papelería'],           
        ];

        foreach ($categorias as $data) {
            Categoria::create([
                'id_user' => $user->id,
                'nombre'  => $data['nombre'],
            ]);
        }
    }
}
