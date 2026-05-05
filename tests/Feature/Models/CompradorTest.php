<?php

namespace Tests\Feature\Models;

use App\Models\Comprador;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompradorTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_many_ventas(): void
    {
        $comprador = Comprador::factory()->create();
        Venta::factory()->count(2)->create(['id_comprador' => $comprador->id]);

        $this->assertCount(2, $comprador->ventas);
    }

    public function test_tipo_comprador_acepta_particular_y_empresa(): void
    {
        $particular = Comprador::factory()->create(['tipo_comprador' => 'particular']);
        $empresa    = Comprador::factory()->create(['tipo_comprador' => 'empresa']);

        $this->assertSame('particular', $particular->fresh()->tipo_comprador);
        $this->assertSame('empresa', $empresa->fresh()->tipo_comprador);
    }
}
