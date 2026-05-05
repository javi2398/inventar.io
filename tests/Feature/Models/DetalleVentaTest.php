<?php

namespace Tests\Feature\Models;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetalleVentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_producto_and_venta(): void
    {
        $producto = Producto::factory()->create();
        $venta    = Venta::factory()->create();
        $detalle  = DetalleVenta::factory()->create([
            'id_producto' => $producto->id,
            'id_venta'    => $venta->id,
        ]);

        $this->assertSame($producto->id, $detalle->producto->id);
        $this->assertSame($venta->id, $detalle->venta->id);
    }
}
