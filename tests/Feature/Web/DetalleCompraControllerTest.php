<?php

namespace Tests\Feature\Web;

use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetalleCompraControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->get('/pedidos')->assertRedirect('/login');
    }

    public function test_store_creates_compra_with_existing_categoria_and_proveedor(): void
    {
        $user      = User::factory()->create();
        $categoria = Categoria::factory()->create(['id_user' => $user->id]);
        $almacen   = Almacen::factory()->create(['id_user' => $user->id]);
        $proveedor = Proveedor::factory()->create();

        $this->actingAs($user)->post('/pedidos', [
            'id_categoria'      => $categoria->id,
            'nombre_categoria'  => null,
            'codigo'            => 'COMP01',
            'nombre'            => 'Producto comprado',
            'descripcion'       => 'desc',
            'perecedero'        => false,
            'imagen'            => null,
            'precio_unitario'   => 8.50,
            'cantidad_actual'   => 30,
            'id_almacen'        => $almacen->id,
            'fecha_vencimiento' => null,
            'id_proveedor'      => $proveedor->id,
            'nombre_proveedor'  => null,
            'telefono'          => null,
            'email'             => null,
        ])->assertOk();

        $this->assertDatabaseHas('productos', ['codigo' => 'COMP01']);
        $this->assertDatabaseHas('compras', [
            'id_user'      => $user->id,
            'id_proveedor' => $proveedor->id,
        ]);

        $producto = Producto::where('codigo', 'COMP01')->first();

        $this->assertDatabaseHas('detalle_compras', [
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 30,
            'estado'          => false,
        ]);
    }

    public function test_addinventario_marks_detalle_estado_and_creates_inventario(): void
    {
        $user      = User::factory()->create();
        $almacen   = Almacen::factory()->create(['id_user' => $user->id]);
        $producto  = Producto::factory()->create();
        $compra    = Compra::factory()->create(['id_user' => $user->id]);

        $detalle = DetalleCompra::factory()->create([
            'id_compra'       => $compra->id,
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 20,
            'precio_unitario' => 7.00,
            'estado'          => false,
        ]);

        $this->actingAs($user)->post('/pedidos/add', [
            'id_producto'       => $producto->id,
            'id_almacen'        => $almacen->id,
            'precio_unitario'   => 7.00,
            'codigo'            => $producto->codigo,
            'id_detalle'        => $detalle->id,
            'cantidad_actual'   => 20,
            'fecha_vencimiento' => null,
        ])->assertOk();

        $this->assertTrue((bool) $detalle->fresh()->estado);

        $this->assertDatabaseHas('inventarios', [
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 20,
            'precio_unitario' => 7.00,
        ]);
    }

    public function test_addinventario_increments_existing_inventario(): void
    {
        $user      = User::factory()->create();
        $almacen   = Almacen::factory()->create(['id_user' => $user->id]);
        $producto  = Producto::factory()->create();
        $compra    = Compra::factory()->create(['id_user' => $user->id]);

        $detalle = DetalleCompra::factory()->create([
            'id_compra'       => $compra->id,
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'precio_unitario' => 7.00,
        ]);

        $inventario = Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'precio_unitario' => 7.00,
            'cantidad_actual' => 5,
        ]);

        $this->actingAs($user)->post('/pedidos/add', [
            'id_producto'       => $producto->id,
            'id_almacen'        => $almacen->id,
            'precio_unitario'   => 7.00,
            'codigo'            => $producto->codigo,
            'id_detalle'        => $detalle->id,
            'cantidad_actual'   => 15,
            'fecha_vencimiento' => null,
        ])->assertOk();

        $this->assertSame(20, $inventario->fresh()->cantidad_actual);
    }

    public function test_destroy_blocks_other_users_compras(): void
    {
        $owner  = User::factory()->create();
        $otro   = User::factory()->create();
        $compra = Compra::factory()->create(['id_user' => $owner->id]);

        $detalle = DetalleCompra::factory()->create(['id_compra' => $compra->id]);

        $this->actingAs($otro)
            ->delete('/pedidos', ['id_detalle' => $detalle->id])
            ->assertForbidden();

        $this->assertDatabaseHas('detalle_compras', ['id' => $detalle->id]);
    }
}
