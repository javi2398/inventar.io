<?php

namespace Tests\Feature\Models;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user_and_proveedor(): void
    {
        $user      = User::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $compra    = Compra::factory()->create([
            'id_user'      => $user->id,
            'id_proveedor' => $proveedor->id,
        ]);

        $this->assertSame($user->id, $compra->user->id);
        $this->assertSame($proveedor->id, $compra->proveedor->id);
    }

    public function test_has_many_detalle_compras(): void
    {
        $compra = Compra::factory()->create();
        DetalleCompra::factory()->count(2)->create(['id_compra' => $compra->id]);

        $this->assertCount(2, $compra->detalleCompras);
    }
}
