<?php

namespace Tests\Feature\Web;

use App\Models\Almacen;
use App\Models\Comprador;
use App\Models\DetalleVenta;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetalleVentaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->get('/ventas')->assertRedirect('/login');
    }

    public function test_store_creates_venta_and_decrements_inventario(): void
    {
        $user      = User::factory()->create();
        $almacen   = Almacen::factory()->create(['id_user' => $user->id]);
        $producto  = Producto::factory()->create(['codigo' => 'VENTA01', 'nombre' => 'Item']);
        $comprador = Comprador::factory()->create();

        $inventario = Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 50,
        ]);

        $this->actingAs($user)->post('/ventas', [
            'id_cliente'      => $comprador->id,
            'tipo_comprador'  => 'particular',
            'codigo'          => 'VENTA01',
            'nombre'          => 'Item',
            'precio_unitario' => 10,
            'id_almacen'      => $almacen->id,
            'cantidad_vendida' => 5,
            'precio_venta'    => 12.50,
        ])->assertOk();

        $this->assertDatabaseHas('ventas', [
            'id_user'      => $user->id,
            'id_comprador' => $comprador->id,
        ]);

        $this->assertDatabaseHas('detalle_ventas', [
            'id_producto'     => $producto->id,
            'cantidad'        => 5,
            'precio_unitario' => 12.50,
        ]);

        $this->assertSame(45, $inventario->fresh()->cantidad_actual);
    }

    public function test_store_creates_new_comprador_when_id_not_provided(): void
    {
        $user     = User::factory()->create();
        $almacen  = Almacen::factory()->create(['id_user' => $user->id]);
        $producto = Producto::factory()->create(['codigo' => 'NEW01', 'nombre' => 'Nuevo']);

        Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 10,
        ]);

        $this->actingAs($user)->post('/ventas', [
            'id_cliente'             => null,
            'nombre_cliente'         => 'Cliente Nuevo',
            'identificacion_cliente' => 'X1234',
            'telefono_cliente'       => '600000000',
            'email_cliente'          => 'nuevo@cliente.com',
            'direccion_cliente'      => 'Calle 1',
            'tipo_comprador'         => 'empresa',
            'codigo'                 => 'NEW01',
            'nombre'                 => 'Nuevo',
            'precio_unitario'        => 5,
            'id_almacen'             => $almacen->id,
            'cantidad_vendida'       => 2,
            'precio_venta'           => 5,
        ])->assertOk();

        $this->assertDatabaseHas('compradores', [
            'email'          => 'nuevo@cliente.com',
            'tipo_comprador' => 'empresa',
        ]);
    }

    public function test_destroy_restores_inventario_and_removes_detalle(): void
    {
        $user      = User::factory()->create();
        $almacen   = Almacen::factory()->create(['id_user' => $user->id]);
        $producto  = Producto::factory()->create();
        $venta     = Venta::factory()->create(['id_user' => $user->id]);

        $detalle = DetalleVenta::factory()->create([
            'id_venta'    => $venta->id,
            'id_producto' => $producto->id,
            'cantidad'    => 4,
        ]);

        $inventario = Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 10,
        ]);

        $this->actingAs($user)
            ->delete('/ventas', ['id_venta' => $detalle->id])
            ->assertOk();

        $this->assertDatabaseMissing('detalle_ventas', ['id' => $detalle->id]);
        $this->assertDatabaseMissing('ventas', ['id' => $venta->id]);
        $this->assertSame(14, $inventario->fresh()->cantidad_actual);
    }

    public function test_destroy_blocks_other_users_ventas(): void
    {
        $owner = User::factory()->create();
        $otro  = User::factory()->create();
        $venta = Venta::factory()->create(['id_user' => $owner->id]);

        $detalle = DetalleVenta::factory()->create(['id_venta' => $venta->id]);

        $this->actingAs($otro)
            ->delete('/ventas', ['id_venta' => $detalle->id])
            ->assertForbidden();

        $this->assertDatabaseHas('detalle_ventas', ['id' => $detalle->id]);
    }
}
