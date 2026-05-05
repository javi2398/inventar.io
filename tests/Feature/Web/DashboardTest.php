<?php

namespace Tests\Feature\Web;

use App\Models\Almacen;
use App\Models\Comprador;
use App\Models\DetalleVenta;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_renders_for_user_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_dashboard_renders_with_data(): void
    {
        $user      = User::factory()->create();
        $producto  = Producto::factory()->create();
        $comprador = Comprador::factory()->create();

        $venta = Venta::factory()->create([
            'id_user'      => $user->id,
            'id_comprador' => $comprador->id,
            'fecha_venta'  => now()->toDateString(),
        ]);

        DetalleVenta::factory()->create([
            'id_venta'        => $venta->id,
            'id_producto'     => $producto->id,
            'cantidad'        => 3,
            'precio_unitario' => 10.00,
        ]);

        Gasto::factory()->create([
            'id_user' => $user->id,
            'precio'  => 50.00,
            'fecha'   => now()->toDateString(),
        ]);

        Almacen::factory()->create(['id_user' => $user->id]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }
}
