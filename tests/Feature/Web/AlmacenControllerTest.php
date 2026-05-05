<?php

namespace Tests\Feature\Web;

use App\Models\Almacen;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlmacenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->get('/inventario')->assertRedirect('/login');
    }

    public function test_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/inventario')->assertOk();
    }

    public function test_store_creates_almacen_for_current_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/inventario', [
            'nombre'    => 'Almacén Test',
            'direccion' => 'Calle Test 1',
        ])->assertOk();

        $this->assertDatabaseHas('almacenes', [
            'id_user'   => $user->id,
            'nombre'    => 'Almacén Test',
            'direccion' => 'Calle Test 1',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/inventario', ['nombre' => '', 'direccion' => ''])
            ->assertSessionHasErrors(['nombre', 'direccion']);
    }

    public function test_delete_removes_empty_almacen(): void
    {
        $user    = User::factory()->create();
        $almacen = Almacen::factory()->create(['id_user' => $user->id]);

        $this->actingAs($user)->delete('/inventario', ['id' => $almacen->id])->assertOk();

        $this->assertDatabaseMissing('almacenes', ['id' => $almacen->id]);
    }

    public function test_delete_removes_associated_inventario(): void
    {
        $user    = User::factory()->create();
        $almacen = Almacen::factory()->create(['id_user' => $user->id]);
        $inv     = Inventario::factory()->create(['id_almacen' => $almacen->id]);

        $this->actingAs($user)->delete('/inventario', ['id' => $almacen->id])->assertOk();

        $this->assertDatabaseMissing('almacenes', ['id' => $almacen->id]);
        $this->assertDatabaseMissing('inventarios', ['id' => $inv->id]);
    }

    public function test_delete_blocks_when_almacen_has_compras(): void
    {
        $user    = User::factory()->create();
        $almacen = Almacen::factory()->create(['id_user' => $user->id]);
        $compra  = Compra::factory()->create(['id_user' => $user->id]);

        DetalleCompra::factory()->create([
            'id_almacen' => $almacen->id,
            'id_compra'  => $compra->id,
        ]);

        $this->actingAs($user)->delete('/inventario', ['id' => $almacen->id])->assertRedirect();

        $this->assertDatabaseHas('almacenes', ['id' => $almacen->id]);
    }
}
