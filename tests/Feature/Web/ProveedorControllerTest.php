<?php

namespace Tests\Feature\Web;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->get('/proveedores')->assertRedirect('/login');
    }

    public function test_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/proveedores')->assertOk();
    }

    public function test_store_creates_proveedor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/proveedor', [
            'nombre'   => 'Distribuciones SL',
            'telefono' => '912345678',
            'email'    => 'contacto@distribuciones.com',
        ])->assertOk();

        $this->assertDatabaseHas('proveedores', [
            'email'  => 'contacto@distribuciones.com',
            'nombre' => 'Distribuciones SL',
        ]);
    }

    public function test_store_validates_telefono_min_length(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/proveedor', [
            'nombre'   => 'X',
            'telefono' => '12345',
            'email'    => 'x@y.com',
        ])->assertSessionHasErrors('telefono');
    }

    public function test_patch_updates_proveedor(): void
    {
        $user      = User::factory()->create();
        $proveedor = Proveedor::factory()->create();

        $this->actingAs($user)->patch('/proveedor', [
            'id_proveedor' => $proveedor->id,
            'nombre'       => 'Nuevo nombre',
            'telefono'     => '900000000',
            'email'        => 'nuevo@correo.com',
        ])->assertOk();

        $this->assertDatabaseHas('proveedores', [
            'id'     => $proveedor->id,
            'nombre' => 'Nuevo nombre',
            'email'  => 'nuevo@correo.com',
        ]);
    }

    public function test_destroy_removes_proveedor_without_compras(): void
    {
        $user      = User::factory()->create();
        $proveedor = Proveedor::factory()->create();

        $this->actingAs($user)
            ->delete('/proveedor', ['id_proveedor' => $proveedor->id])
            ->assertOk();

        $this->assertDatabaseMissing('proveedores', ['id' => $proveedor->id]);
    }

    public function test_destroy_blocks_when_proveedor_has_compras_for_user(): void
    {
        $user      = User::factory()->create();
        $proveedor = Proveedor::factory()->create();
        Compra::factory()->create([
            'id_user'      => $user->id,
            'id_proveedor' => $proveedor->id,
        ]);

        $this->actingAs($user)
            ->delete('/proveedor', ['id_proveedor' => $proveedor->id])
            ->assertRedirect();

        $this->assertDatabaseHas('proveedores', ['id' => $proveedor->id]);
    }
}
