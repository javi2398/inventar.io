<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index($id)
    {
        $user = Auth::user();


        $productoId = $id;

        $producto = Producto::with('categoria')->findOrFail($productoId);

        // Stock disponible
        $stock = Inventario::where('id_producto', $productoId)->sum('cantidad_actual');

        // Ventas del producto por semana (último mes)
        $ventasPorSemana = DetalleVenta::select(
                DB::raw('WEEK(ventas.fecha_venta) as semana'),
                DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as total')
            )
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
            ->where('ventas.id_user', $user->id)
            ->where('detalle_ventas.id_producto', $productoId)
            ->whereBetween('ventas.fecha_venta', [now()->subWeeks(4), now()])
            ->groupBy('semana')
            ->orderBy('semana')
            ->get();

        // Distribución de ventas: vendido vs stock
        $totalVendido = DetalleVenta::join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
            ->where('ventas.id_user', $user->id)
            ->where('detalle_ventas.id_producto', $productoId)
            ->sum('detalle_ventas.cantidad');

        // Precio promedio (para estimaciones)
        $precioPromedio = DetalleVenta::where('id_producto', $productoId)->avg('precio_unitario') ?? 0;

        // Beneficio neto estimado
        $beneficioEstimado = $totalVendido * $precioPromedio;

        // Ventas estimadas para este mes (simple proyección lineal por semana)
        $ventasEstimadasMes = $ventasPorSemana->avg('total') > 0
            ? round($ventasPorSemana->avg('total') * 4 / $precioPromedio)
            : 0;

        // Tendencia del stock por mes (últimos 5 meses)
        $stockTendencia = Inventario::select(
                DB::raw('DATE_FORMAT(fecha_entrada, "%b") as mes'),
                DB::raw('SUM(cantidad_actual) as total_stock')
            )
            ->where('id_producto', $productoId)
            ->where('fecha_entrada', '>=', now()->subMonths(4))
            ->groupBy('mes')
            ->orderByRaw('MIN(fecha_entrada)')
            ->get();


            $meses = collect();
            for ($i = 5; $i >= 0; $i--) {
                $meses->push([
                    'mes' => now()->subMonths($i)->format('M'), // Ej: Ene, Feb
                    'total_stock' => 0
                ]);
            }

            // Reemplazar los valores si existen datos
            foreach ($stockTendencia as $dato) {
                $meses = $meses->map(function ($mes) use ($dato) {
                    return $mes['mes'] === $dato->mes
                        ? ['mes' => $mes['mes'], 'total_stock' => $dato->total_stock]
                        : $mes;
                });
            }

        return Inertia::render('Producto', [
            'producto' => $producto,
            'stock' => $stock,
            'ventas_por_semana' => $ventasPorSemana,
            'total_vendido' => $totalVendido,
            'stock_vs_venta' => [
                'vendido' => $totalVendido,
                'stock' => $stock
            ],
            'beneficio_estimado' => round($beneficioEstimado, 2),
            'ventas_estimadas_mes' => $ventasEstimadasMes,
            'stock_tendencia' => $stockTendencia,
            'stock_tendencia_chart' => [
                'labels' => $meses->pluck('mes'),
                'data' => $meses->pluck('total_stock'),
            ],
        ]);

    }

    public function defaultIndex()
    {
        $user = Auth::user();

        $almacenesQuery = Almacen::with(['productos' => function ($query) {
            $query->withPivot('id_almacen', 'cantidad_actual', 'precio_unitario', 'fecha_entrada', 'fecha_salida');
        }])
        ->where('id_user', $user->id);

        $allProductos = [];

        $almacenes = $almacenesQuery->get()->map(function ($almacen) use (&$allProductos, $user) {
            $productos = $almacen->productos;

            $precioTotal = $productos->sum(fn($producto) =>
                $producto->pivot->cantidad_actual * $producto->pivot->precio_unitario
            );

            $cantidadTotal = $productos->sum(fn($producto) =>
                $producto->pivot->cantidad_actual
            );

            $productosData = $productos->map(function ($producto) use ($almacen, $user) {
                // Aquí usamos el método para obtener proveedores por producto
                $proveedores = $producto->proveedores($user->id)->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nombre' => $p->nombre,
                        'telefono' => $p->telefono,
                        'email' => $p->email,
                    ];
                })->values();

                return [
                    'id_producto' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'descripcion' =>$producto->descripcion,
                    'imagen' =>$producto->imagen,
                    'precio_unitario' => $producto->pivot->precio_unitario,
                    'cantidad_actual' => $producto->pivot->cantidad_actual,
                    'fecha_entrada' => $producto->pivot->fecha_entrada,
                    'fecha_salida' => $producto->pivot->fecha_salida,
                    'almacen_id' => $almacen->id,
                    'almacen_nombre' => $almacen->nombre,
                    'proveedores' => $proveedores->toArray(),
                    'perecedero' =>$producto->perecedero,
                ];
            })->toArray();

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

        return Inertia::render('Producto',[
            'all_products' => $allProductos,
            'all_almacenes' => $almacenes
        ]);
    }

    public function delete(Request $request)
    {
        $datos = $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'id_almacen' =>'required|exists:almacenes,id',
            'precio_unitario' => 'required|numeric'
        ]);

        $inventario = Inventario::where('id_producto',$datos['id_producto'])
            ->where('id_almacen',$datos['id_almacen'])
            ->where('precio_unitario',$datos['precio_unitario'])
            ->first();
            
        $inventario->delete();

        return redirect()->route('inventario.index');

    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            // datos para crear producto
            'codigo' => 'required|string|unique:productos,codigo',
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'imagen' => 'required|image|mimes:png,jpeg,jpg|max:2048',
            //datos para crear inventario
            'id_almacen' => 'required|exists:almacenes,id',
            'cantidad_actual' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:1',
            //datos para la categoria
            'id_categoria' => 'nullable|exists:categorias,id',
            'nombre_categoria' => 'nullable|string',
            'perecedero' => 'nullable|boolean'
        ]);

        $categoria = Categoria::where('id',$data['id_categoria'])
            ->where('id_user',$user->id)
            ->first();

        if(!$categoria){
            $newCategoria = Categoria::create([
                'id_user' => $user->id,
                'nombre' => $data['nombre_categoria'],
                'perecedero'=> $data['perecedero'],
                'fecha_vencimiento' => $data['perecedero'] ? now()->addDays(14)->toDate() : null
            ]);
        }

        $producto = Producto::where('codigo',$data['codigo'])->where('nombre',$data['nombre'])->first();
        
        if(!$producto){
            $producto = Producto::create([
                'id_categoria' => $data['id_categoria'] ? $data['id_categoria'] : $newCategoria->id,
                'codigo' => $data['codigo'],
                'nombre'=> $data['nombre'],
                'descripcion'=> $data['descripcion'],
                'imagen'=> $data['imagen']
            ]);
        }

        Inventario::create([
            'id_producto' => $producto->id,
            'id_almacen' => $data['id_almacen'],
            'cantidad_actual' => $data['cantidad_actual'],
            'precio_unitario' => $data['precio_unitario'],
            'fecha_entrada' => now(),
            'fecha_salida' => null
        ]);

        return redirect()->route('inventario.index');
        
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // datos para crear producto
            'codigo' => 'nullable|string',
            'nombre' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|string',
            'perecedero' => 'nullable|boolean',
            
            'imagen' => 'nullable|string',
            //datos para crear inventario
            'id_almacen' => 'required|exists:almacenes,id',
            'id_almacen_antiguo' => 'required|exists:almacenes,id',

            'id_producto' => 'required|exists:productos,id',

            'cantidad_actual' => 'nullable|integer|min:1',
            'precio_unitario' => 'nullable|numeric|min:1',
            //datos para la categoria
            'id_categoria' => 'nullable|exists:categorias,id',
            'nombre_categoria' => 'nullable|string',
        ]);

        $user = Auth::user();
        $producto = Producto::find($validated['id_producto']);
        if ($producto) {
            $producto->update([
                'id_categoria' => $validated['id_categoria'] ?? $producto->id_categoria,
                'codigo' => $validated['codigo'] ?? $producto->codigo,
                'nombre' => $validated['nombre'] ?? $producto->nombre,
                'descripcion' => $validated['descripcion'] ?? $producto->descripcion,
                'imagen' => $validated['imagen'] ?? $producto->imagen,
                'perecedero'=>$validated['perecedero'] ?? $producto->perecedero
            ]);
        }


        $inventario = Inventario::where('id_producto', $producto->id)
            ->where('id_almacen', $validated['id_almacen_antiguo'])
            ->first();

        if ($inventario) {
            $inventario->update([
                'cantidad_actual' => $validated['cantidad_actual'] ?? $inventario->cantidad_actual,
                'precio_unitario' => $validated['precio_unitario'] ?? $inventario->precio_unitario,
                'id_almacen' => $validated['id_almacen'] ?? $inventario->id_almacen,
                'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? $inventario->fecha_vencimiento,
            ]);
        }

        return $this->renderInventario($user);
    }

    public function patch (Request $request){

        $user = Auth::user();
        $datos = $request ->validate([
            'id_almacen' => 'required|integer|exists:almacenes,id',
            'id_producto' => 'required|integer|exists:productos,id',
            'cantidad_actual' => 'required|integer'
        ]);

        $almacen = Almacen::where('id',$datos['id_almacen'])
            ->where('id_user',$user->id)
            ->first();

        $inventario = Inventario::where('id_producto', $datos['id_producto'])
            ->where('id_almacen', $almacen->id)
            ->first();

        $inventario->cantidad_actual = $datos['cantidad_actual'];
        $inventario->save();

        return $this->renderInventario($user);


    }


    private function calcularStockStats($almacenes)
    {
        $stats = ['disponible' => 0, 'lowStock' => 0, 'agotado' => 0];

        foreach ($almacenes as $almacen) {
            foreach ($almacen['productos'] as $producto) {
                $cantidad = $producto['cantidad_actual'];
                match (true) {
                    $cantidad === 0 => $stats['agotado']++,
                    $cantidad < 10 => $stats['lowStock']++,
                    default => $stats['disponible']++,
                };
            }
        }

        return $stats;
    }

    public function renderInventario($user, $almacenesIds = null)
    {
        $almacenes = $this->obtenerAlmacenesConProductos($user->id, $almacenesIds);
        $stats = $this->calcularStockStats($almacenes);
        $allProductos = collect($almacenes)->pluck('productos')->flatten(1)->values();
        $allProveedores = $this->obtenerProveedores($user->id);
        $categorias = Categoria::where('id_user', $user->id)->with('productos')->get();
        

        $detallesVentasRaw = DetalleVenta::with(['producto', 'venta.comprador'])
            ->whereHas('venta', function ($query) use ($user) {
                $query->where('id_user', $user->id);
            })
            ->get();

        $allClientes = $detallesVentasRaw
            ->pluck('venta.comprador') 
            ->filter() 
            ->unique('id') 
            ->values();

        $alm = Almacen::with('productos')->where('id_user', $user->id)->get();
        // Obtener el total de productos unicos
        $totalProductos = $alm->pluck('productos')
        ->flatten() 
        ->count();

        return Inertia::render('Inventario', [
            'status' => true,
            'message' => 'Almacenes encontrados',
            'count' => count($almacenes),
            'total_productos' => $totalProductos,
            'total_unidades' => collect($almacenes)->sum('cantidad_total'),
            'total_precio' => collect($almacenes)->sum('precio_total'),
            'disponible' => $stats['disponible'],
            'lowStock' => $stats['lowStock'],
            'agotado' => $stats['agotado'],
            'data' => $almacenes,
            'all_productos' => $allProductos,
            'all_proveedores' => $allProveedores,
            'all_clientes' => $allClientes,
            'categorias' => $categorias,
        ]);
    }

    private function obtenerAlmacenesConProductos($userId)
    {
        $query = Almacen::with(['productos' => function ($q) {
            $q->withPivot('id_almacen', 'cantidad_actual', 'precio_unitario', 'fecha_entrada', 'fecha_salida');
        }])->where('id_user', $userId);

        return $query->get()->map(function ($almacen) {
            $productos = $almacen->productos;

            $productosData = $productos->map(function ($producto) use ($almacen) {
                // Obtener proveedores únicos para el producto
                $proveedores = DetalleCompra::with('compra.proveedor')
                    ->where('id_producto', $producto->id)
                    ->get()
                    ->pluck('compra.proveedor')
                    ->filter()
                    ->unique('id')
                    ->values()
                    ->map(function ($proveedor) {
                        return [
                            'id' => $proveedor->id,
                            'nombre' => $proveedor->nombre,
                            'telefono' => $proveedor->telefono,
                            'email' => $proveedor->email,
                        ];
                    });

                return [
                    'id_categoria' => $producto->categoria->id,
                    'categoria' => $producto->categoria->nombre,

                    'id_producto' => $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'imagen' => $producto->imagen,
                    'descripcion' => $producto->descripcion,
                    'fecha_entrada' => $producto->pivot->fecha_entrada,
                    'fecha_salida' => $producto->pivot->fecha_salida,
                    
                    'id_almacen' => $almacen->id,
                    'almacen_nombre' => $almacen->nombre,
                    'precio_unitario' => $producto->pivot->precio_unitario,
                    'cantidad_actual' => $producto->pivot->cantidad_actual,
                    'fecha_vencimiento'=> $producto->pivot->fecha_vencimiento,
                    'proveedores' => $proveedores,
                ];
            })->toArray();

            return [
                'id' => $almacen->id,
                'nombre' => $almacen->nombre,
                'direccion' => $almacen->direccion,
                'productos_count' => count($productos),
                'cantidad_total' => $productos->sum('pivot.cantidad_actual'),
                'precio_total' => $productos->sum(fn($p) => $p->pivot->cantidad_actual * $p->pivot->precio_unitario),
                'productos' => $productosData,
            ];
        });
    }

    private function obtenerProveedores($userId)
    {
        return DetalleCompra::with(['producto', 'compra.proveedor'])
            ->whereHas('compra', fn($q) => $q->where('id_user', $userId))
            ->get()
            ->pluck('compra.proveedor')
            ->filter()
            ->unique('id')
            ->values();
    }
}
