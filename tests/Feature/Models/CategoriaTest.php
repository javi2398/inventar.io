<?php

namespace Tests\Feature\Models;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create(['id_user' => $user->id]);

        $this->assertInstanceOf(User::class, $categoria->user);
        $this->assertSame($user->id, $categoria->user->id);
    }

    public function test_has_many_productos(): void
    {
        $categoria = Categoria::factory()->create();
        Producto::factory()->count(3)->create(['id_categoria' => $categoria->id]);

        $this->assertCount(3, $categoria->productos);
    }
}
