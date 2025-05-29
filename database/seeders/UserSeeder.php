<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            ['name' => 'user', 'email' => 'user@gmail.com', 'password' => 'password'],
            ['name' => 'user1', 'email' => 'user1@gmail.com', 'password' => 'password']
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }
    }
}
