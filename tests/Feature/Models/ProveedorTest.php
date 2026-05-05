<?php

namespace Tests\Feature\Models;

use App\Models\Compra;
use App\Models\Proveedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedorTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_many_compras(): void
    {
        $proveedor = Proveedor::factory()->create();
        Compra::factory()->count(2)->create(['id_proveedor' => $proveedor->id]);

        $this->assertCount(2, $proveedor->compras);
    }
}
