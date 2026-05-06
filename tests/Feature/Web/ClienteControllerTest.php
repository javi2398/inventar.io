<?php

namespace Tests\Feature\Web;

use App\Models\Comprador;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_comprador(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/entidades/cliente', [
            'nombre'         => 'Juan Pérez',
            'identificacion' => '12345678X',
            'telefono'       => '600000000',
            'email'          => 'juan@example.com',
            'direccion'      => 'Calle Falsa 123',
            'tipo_comprador' => 'particular',
        ])->assertOk();

        $this->assertDatabaseHas('compradores', [
            'email'          => 'juan@example.com',
            'tipo_comprador' => 'particular',
        ]);
    }

    public function test_store_rejects_invalid_tipo_comprador(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/entidades/cliente', [
            'nombre'         => 'Juan',
            'tipo_comprador' => 'mayorista',
        ])->assertSessionHasErrors('tipo_comprador');
    }

    public function test_patch_updates_comprador(): void
    {
        $user      = User::factory()->create();
        $comprador = Comprador::factory()->create(['tipo_comprador' => 'particular']);

        $this->actingAs($user)->post('/entidades/cliente', [
            'id_cliente'     => $comprador->id,
            'nombre'         => 'Nombre actualizado',
            'identificacion' => 'ABC123',
            'telefono'       => '611111111',
            'email'          => 'nuevo@example.com',
            'direccion'      => 'Otra dirección',
            'tipo_comprador' => 'empresa',
        ])->assertOk();

        $this->assertDatabaseHas('compradores', [
            'id'             => $comprador->id,
            'nombre'         => 'Nombre actualizado',
            'tipo_comprador' => 'empresa',
        ]);
    }

    public function test_destroy_removes_comprador_without_ventas(): void
    {
        $user      = User::factory()->create();
        $comprador = Comprador::factory()->create();

        $this->actingAs($user)
            ->delete('/entidades/cliente', ['id_cliente' => $comprador->id])
            ->assertOk();

        $this->assertDatabaseMissing('compradores', ['id' => $comprador->id]);
    }

    public function test_destroy_blocks_when_comprador_has_ventas_for_user(): void
    {
        $user      = User::factory()->create();
        $comprador = Comprador::factory()->create();
        Venta::factory()->create([
            'id_user'      => $user->id,
            'id_comprador' => $comprador->id,
        ]);

        $this->actingAs($user)
            ->delete('/entidades/cliente', ['id_cliente' => $comprador->id])
            ->assertRedirect();

        $this->assertDatabaseHas('compradores', ['id' => $comprador->id]);
    }
}
