<?php

namespace Tests\Feature\Models;

use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_categoria(): void
    {
        $categoria = Categoria::factory()->create();
        $producto = Producto::factory()->create(['id_categoria' => $categoria->id]);

        $this->assertInstanceOf(Categoria::class, $producto->categoria);
        $this->assertSame($categoria->id, $producto->categoria->id);
    }

    public function test_has_many_inventarios(): void
    {
        $producto = Producto::factory()->create();
        Inventario::factory()->count(2)->create(['id_producto' => $producto->id]);

        $this->assertCount(2, $producto->inventarios);
    }

    public function test_belongs_to_many_almacenes(): void
    {
        $producto = Producto::factory()->create();
        $almacen = Almacen::factory()->create();

        Inventario::factory()->create([
            'id_producto' => $producto->id,
            'id_almacen'  => $almacen->id,
        ]);

        $this->assertCount(1, $producto->almacenes);
        $this->assertSame($almacen->id, $producto->almacenes->first()->id);
    }

    public function test_has_many_detalle_ventas_y_compras(): void
    {
        $producto = Producto::factory()->create();
        DetalleVenta::factory()->create(['id_producto' => $producto->id]);
        DetalleCompra::factory()->create(['id_producto' => $producto->id]);

        $this->assertCount(1, $producto->detalleventas);
        $this->assertCount(1, $producto->detallecompras);
    }

    public function test_proveedores_returns_only_those_for_given_user(): void
    {
        $user      = User::factory()->create();
        $otroUser  = User::factory()->create();
        $producto  = Producto::factory()->create();
        $proveedor = Proveedor::factory()->create();

        $compra = Compra::factory()->create([
            'id_user'      => $user->id,
            'id_proveedor' => $proveedor->id,
        ]);

        DetalleCompra::factory()->create([
            'id_compra'   => $compra->id,
            'id_producto' => $producto->id,
        ]);

        // Otra compra del mismo producto pero otro user: no debe aparecer.
        $compraOtro = Compra::factory()->create(['id_user' => $otroUser->id]);
        DetalleCompra::factory()->create([
            'id_compra'   => $compraOtro->id,
            'id_producto' => $producto->id,
        ]);

        $proveedores = $producto->proveedores($user->id);

        $this->assertCount(1, $proveedores);
        $this->assertSame($proveedor->id, $proveedores->first()->id);
    }
}
