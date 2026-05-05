<?php

namespace Tests\Feature\Models;

use App\Models\Almacen;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetalleCompraTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_producto_compra_almacen(): void
    {
        $producto = Producto::factory()->create();
        $compra   = Compra::factory()->create();
        $almacen  = Almacen::factory()->create();
        $detalle  = DetalleCompra::factory()->create([
            'id_producto' => $producto->id,
            'id_compra'   => $compra->id,
            'id_almacen'  => $almacen->id,
        ]);

        $this->assertSame($producto->id, $detalle->producto->id);
        $this->assertSame($compra->id, $detalle->compra->id);
        $this->assertSame($almacen->id, $detalle->almacen->id);
    }

    public function test_subtotal_accessor_multiplies_cantidad_by_precio(): void
    {
        $detalle = DetalleCompra::factory()->make([
            'cantidad_actual' => 4,
            'precio_unitario' => 25.00,
        ]);

        $this->assertEquals(100.00, $detalle->subtotal);
    }
}
