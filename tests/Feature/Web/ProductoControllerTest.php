<?php

namespace Tests\Feature\Web;

use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_requires_authentication(): void
    {
        $producto = Producto::factory()->create();

        $this->get('/detalles/' . $producto->id)->assertRedirect('/login');
    }

    public function test_show_returns_404_for_missing_producto(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/detalles/999999')->assertNotFound();
    }

    public function test_store_creates_producto_inventario_in_existing_categoria(): void
    {
        $user      = User::factory()->create();
        $categoria = Categoria::factory()->create(['id_user' => $user->id]);
        $almacen   = Almacen::factory()->create(['id_user' => $user->id]);

        $this->actingAs($user)->post('/inventario/producto', [
            'codigo'           => 'TST001',
            'nombre'           => 'Producto test',
            'descripcion'      => 'Descripción test',
            'imagen'           => UploadedFile::fake()->create('producto.jpg', 100, 'image/jpeg'),
            'id_almacen'       => $almacen->id,
            'cantidad_actual'  => 25,
            'precio_unitario'  => 9.99,
            'id_categoria'     => $categoria->id,
            'perecedero'       => false,
        ])->assertRedirect(route('inventario.index'));

        $this->assertDatabaseHas('productos', [
            'codigo'       => 'TST001',
            'id_categoria' => $categoria->id,
        ]);

        $producto = Producto::where('codigo', 'TST001')->first();

        $this->assertDatabaseHas('inventarios', [
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 25,
        ]);
    }

    public function test_store_validates_codigo_uniqueness(): void
    {
        $user    = User::factory()->create();
        $almacen = Almacen::factory()->create(['id_user' => $user->id]);
        Producto::factory()->create(['codigo' => 'DUP001']);

        $this->actingAs($user)->post('/inventario/producto', [
            'codigo'          => 'DUP001',
            'nombre'          => 'Otro',
            'descripcion'     => 'Otro',
            'imagen'          => UploadedFile::fake()->create('producto.jpg', 100, 'image/jpeg'),
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 1,
            'precio_unitario' => 1,
        ])->assertSessionHasErrors('codigo');
    }

    public function test_delete_removes_matching_inventario_only(): void
    {
        $user    = User::factory()->create();
        $almacen = Almacen::factory()->create(['id_user' => $user->id]);
        $otroAlmacen = Almacen::factory()->create(['id_user' => $user->id]);
        $producto = Producto::factory()->create();

        $inventarioBorrar = Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'precio_unitario' => 5.50,
        ]);

        $inventarioMantener = Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $otroAlmacen->id,
            'precio_unitario' => 5.50,
        ]);

        $this->actingAs($user)->delete('/inventario/producto', [
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'precio_unitario' => 5.50,
        ])->assertRedirect();

        $this->assertDatabaseMissing('inventarios', ['id' => $inventarioBorrar->id]);
        $this->assertDatabaseHas('inventarios', ['id' => $inventarioMantener->id]);
    }

    public function test_patch_updates_cantidad_actual(): void
    {
        $user       = User::factory()->create();
        $almacen    = Almacen::factory()->create(['id_user' => $user->id]);
        $producto   = Producto::factory()->create();
        $inventario = Inventario::factory()->create([
            'id_producto'     => $producto->id,
            'id_almacen'      => $almacen->id,
            'cantidad_actual' => 100,
        ]);

        $this->actingAs($user)->patch('/inventario/producto', [
            'id_almacen'      => $almacen->id,
            'id_producto'     => $producto->id,
            'cantidad_actual' => 42,
        ])->assertOk();

        $this->assertDatabaseHas('inventarios', [
            'id'              => $inventario->id,
            'cantidad_actual' => 42,
        ]);
    }
}
