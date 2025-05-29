<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;

use Inertia\Inertia;
use App\Models\Venta;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Comprador;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use Illuminate\Support\Facades\Auth;

class DetallesVentaController extends Controller
{
    //

    public function index(){
        $user = Auth::user();
        return $this->renderInventario($user);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $datos = $request->validate([
            'id_venta' => 'required|exists:detalle_ventas,id'
        ]);

        $detalleVenta = DetalleVenta::with('venta', 'producto')->findOrFail($datos['id_venta']);

        if ($detalleVenta->venta->id_user !== $user->id) {
            abort(403, 'No tienes permiso para eliminar esta venta.');
        }

        $inventario = Inventario::where('id_producto', $detalleVenta->id_producto)
            ->where('id_almacen', function ($query) use ($user) {
                $query->select('id')
                    ->from('almacenes')
                    ->where('id_user', $user->id)
                    ->limit(1);
            })
            ->orderByDesc('fecha_entrada')
            ->first();

        if ($inventario) {
            $inventario->increment('cantidad_actual', $detalleVenta->cantidad);
        }

        $venta = $detalleVenta->venta;

        $detalleVenta->delete();

        if ($venta->detalleVentas()->count() === 0) {
            $venta->delete();
        }

        return $this->renderInventario($user);
    }

    public function store(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            // Cliente
            'id_cliente' => 'nullable|exists:compradores,id',
            'nombre_cliente' => 'nullable|string|min:1',
            'identificacion_cliente' => 'nullable|string|min:1',
            'telefono_cliente' => 'nullable|string|min:1',
            'email_cliente' => 'nullable|email',
            'direccion_cliente' => 'nullable|string|min:1',
            'tipo_comprador' => 'in:particular,empresa',

            // Producto
            'codigo' => 'nullable|string|min:1',
            'nombre' => 'nullable|string|min:1',
            'precio_unitario' => 'nullable|numeric|min:1',
            'id_almacen' => 'nullable|exists:almacenes,id',

            // Detalles de venta
            'cantidad_vendida' => 'required|integer|min:1',
            'precio_venta' => 'required|numeric'
        ]);

        $producto = Producto::where('codigo', $datos['codigo'])
            ->where('nombre', $datos['nombre'])
            ->first();

        $almacen = Almacen::where('id_user', $user->id)
            ->where('id', $datos['id_almacen'])
            ->first();

        $comprador = Comprador::where('id',$datos['id_cliente'])
        ->first();

        if(!$comprador){
            $comprador = Comprador::create([
                'nombre' => $datos['nombre_cliente'],
                'identificacion' =>$datos['identificacion_cliente'],
                'telefono' => $datos['telefono_cliente'],
                'email' =>$datos['email_cliente'] ,
                'direccion' =>$datos['direccion_cliente'] ,
                'tipo_comprador' => $datos['tipo_comprador'],
            ]);
        }

        // Buscar inventario suficiente
        $inventario = Inventario::where('id_producto', $producto->id)
            ->where('id_almacen', $almacen->id)
            ->orderByDesc('fecha_entrada')
            ->first();

        if (!$inventario) {
            return redirect()->back()->withErrors('No hay suficiente stock disponible para este producto.');
        }

        $venta = Venta::create([
            'id_user' => $user->id,
            'id_comprador' => $comprador->id,
            'fecha_venta' => now(),
        ]);

        DetalleVenta::create([
            'id_producto' => $producto->id,
            'id_venta' => $venta->id,
            'cantidad' => $datos['cantidad_vendida'],
            'precio_unitario' => $datos['precio_venta']
        ]);

        $inventario->decrement('cantidad_actual', $datos['cantidad_vendida']);

        return $this->renderInventario($user);
    }

    public function renderInventario($user){
        $almacenesQuery = Almacen::with(['productos' => function ($query) {
            $query->withPivot('id_almacen','cantidad_actual', 'precio_unitario', 'fecha_entrada', 'fecha_salida');
        }])
        ->where('id_user', $user->id);

        $allProductos = [];

        $almacenes = $almacenesQuery->get()->map(function ($almacen) use (&$allProductos) {
            $productos = $almacen->productos;

            $precioTotal = $productos->sum(fn($producto) =>
                $producto->pivot->cantidad_actual * $producto->pivot->precio_unitario
            );

            $cantidadTotal = $productos->sum(fn($producto) =>
                $producto->pivot->cantidad_actual
            );

            $productosData = $productos->map(function ($producto) use ($almacen) {
                return [
                    'id' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'precio_unitario' => $producto->pivot->precio_unitario,
                    'cantidad_actual' => $producto->pivot->cantidad_actual,
                    'fecha_entrada' => $producto->pivot->fecha_entrada,
                    'fecha_salida' => $producto->pivot->fecha_salida,
                    'imagen' => $producto->imagen,
                    'almacen_id' => $almacen->id,
                    'almacen_nombre' => $almacen->nombre,
                ];
            })->toArray();

            // Acumular productos en la lista global
            $allProductos = array_merge($allProductos, $productosData);

            return [
                'id' => $almacen->id,
                'nombre' => $almacen->nombre,
                'direccion' => $almacen->direccion,
                'productos_count' => $productos->count(),
                'cantidad_total' => $cantidadTotal,
                'precio_total' => $precioTotal,
                'productos' => $productosData,
            ];
        });

        $detallesVentasRaw = DetalleVenta::with(['producto', 'venta.comprador'])
            ->whereHas('venta', function ($query) use ($user) {
                $query->where('id_user', $user->id);
            })
            ->get();

        $detallesVentas = $detallesVentasRaw->map(function ($detalle) {
            return [
                'id_detalle' => $detalle->id,
                'producto_id' => $detalle->id_producto,
                'codigo' => $detalle->producto->codigo,
                'nombre' => $detalle->producto->nombre,
                'precio_unitario' => $detalle->precio_unitario,
                'cantidad' => $detalle->cantidad,
                'fecha_venta' => optional($detalle->venta)->fecha_venta,
                'cliente' => optional($detalle->venta->comprador)->nombre,
            ];
        });

        $allClientes = $detallesVentasRaw
            ->pluck('venta.comprador') 
            ->filter() 
            ->unique('id') 
            ->values();

        $allAlmacenes = Almacen::with(['productos' => function ($query) {
            $query->withPivot('id_almacen','cantidad_actual', 'precio_unitario', 'fecha_entrada', 'fecha_salida');
        }])
        ->where('id_user', $user->id)->get();

        $categorias = Categoria::where('id_user', $user->id)->with('productos')->get();

        return Inertia::render('Ventas', props: [
            'status' => true,
            'message' => 'Almacenes encontrados',
            'count' => $almacenes->count(),
            'data' => $almacenes,
            'all_clientes' => $allClientes,
            'all_productos' => $allProductos,
            'all_almacenes' => $allAlmacenes,
            'detalles_ventas' => $detallesVentas,
            'categorias' => $categorias,
        ]);
    }
}
