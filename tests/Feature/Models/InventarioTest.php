<?php

namespace Tests\Feature\Models;

use App\Models\Almacen;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_producto_and_almacen(): void
    {
        $producto  = Producto::factory()->create();
        $almacen   = Almacen::factory()->create();
        $inventario = Inventario::factory()->create([
            'id_producto' => $producto->id,
            'id_almacen'  => $almacen->id,
        ]);

        $this->assertSame($producto->id, $inventario->producto->id);
        $this->assertSame($almacen->id, $inventario->almacenes->id);
    }

    public function test_subtotal_accessor_multiplies_cantidad_by_precio(): void
    {
        $inventario = Inventario::factory()->make([
            'cantidad_actual' => 7,
            'precio_unitario' => 12.50,
        ]);

        $this->assertEquals(87.50, $inventario->subtotal);
    }
}
