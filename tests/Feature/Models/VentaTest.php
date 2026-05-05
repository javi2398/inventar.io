<?php

namespace Tests\Feature\Models;

use App\Models\Comprador;
use App\Models\DetalleVenta;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user_and_comprador(): void
    {
        $user      = User::factory()->create();
        $comprador = Comprador::factory()->create();
        $venta     = Venta::factory()->create([
            'id_user'      => $user->id,
            'id_comprador' => $comprador->id,
        ]);

        $this->assertSame($user->id, $venta->user->id);
        $this->assertSame($comprador->id, $venta->comprador->id);
    }

    public function test_has_many_detalle_ventas(): void
    {
        $venta = Venta::factory()->create();
        DetalleVenta::factory()->count(2)->create(['id_venta' => $venta->id]);

        $this->assertCount(2, $venta->detalleVentas);
    }
}
