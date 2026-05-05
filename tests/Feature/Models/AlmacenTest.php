<?php

namespace Tests\Feature\Models;

use App\Models\Almacen;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlmacenTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $almacen = Almacen::factory()->create(['id_user' => $user->id]);

        $this->assertInstanceOf(User::class, $almacen->user);
        $this->assertSame($user->id, $almacen->user->id);
    }

    public function test_has_many_productos_through_inventarios_pivot(): void
    {
        $almacen = Almacen::factory()->create();
        $producto = Producto::factory()->create();

        Inventario::factory()->create([
            'id_almacen'      => $almacen->id,
            'id_producto'     => $producto->id,
            'cantidad_actual' => 50,
            'precio_unitario' => 10.00,
        ]);

        $this->assertCount(1, $almacen->productos);
        $this->assertSame($producto->id, $almacen->productos->first()->id);
        $this->assertSame(50, $almacen->productos->first()->pivot->cantidad_actual);
    }
}
