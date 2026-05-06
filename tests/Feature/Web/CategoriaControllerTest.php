<?php

namespace Tests\Feature\Web;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_categoria_for_current_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/entidades/categoria', ['nombre' => 'Bebidas'])
            ->assertOk();

        $this->assertDatabaseHas('categorias', [
            'id_user' => $user->id,
            'nombre'  => 'Bebidas',
        ]);
    }

    public function test_store_validates_nombre_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/entidades/categoria', ['nombre' => ''])
            ->assertSessionHasErrors('nombre');
    }

    public function test_destroy_removes_empty_categoria(): void
    {
        $user      = User::factory()->create();
        $categoria = Categoria::factory()->create(['id_user' => $user->id]);

        $this->actingAs($user)
            ->delete('/entidades/categoria', ['id_categoria' => $categoria->id])
            ->assertOk();

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_destroy_blocks_when_categoria_has_productos(): void
    {
        $user      = User::factory()->create();
        $categoria = Categoria::factory()->create(['id_user' => $user->id]);
        Producto::factory()->create(['id_categoria' => $categoria->id]);

        $this->actingAs($user)
            ->delete('/entidades/categoria', ['id_categoria' => $categoria->id])
            ->assertStatus(422);

        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }
}
