<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Compra;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use App\Models\DetalleCompra;

class DetallesCompraController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $this->renderInventario($user);
    }

    public function patch(Request $request){
        $user = Auth::user();
        $datos = $request->validate([
            'id_detalle' => 'required|integer|exists:detalle_compras,id',
            //producto que pides
            'id_categoria' => 'nullable|integer',
            'codigo' => 'required|string',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'perecedero' => 'nullable|boolean',
            'imagen' => 'nullable|string',

            //Inventario
            'precio_unitario' => 'required|numeric|min:0',
            'cantidad_actual' => 'required|integer|min:1',
            'id_almacen' => 'required|exists:almacenes,id',
            'fecha_vencimiento' => 'nullable|date',

            //Proveedor
            'id_proveedor' => 'nullable',
            'nombre_proveedor' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email',

            //datos para crear la categoria
            'nombre_categoria' => 'nullable|string',
        ]);


        //Busca o crea la categoría
        $categoria = Categoria::where('id_user', $user->id)
            ->where(function ($query) use ($datos) {
                $query->where('nombre', $datos['nombre_categoria'])
                    ->orWhere('id', $datos['id_categoria']);
            })
            ->first();

        if (!$categoria) {
            $categoria = Categoria::create([
                'id_user' => $user->id,
                'nombre' => $datos['nombre_categoria'],
            ]);
        }

        //Busca o crear producto
        $producto = Producto::updateOrCreate(
            [
                'codigo' => $datos['codigo'],
                'id_categoria' => $categoria->id,
            ],
            [
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'],
                'perecedero' => $datos['perecedero'],
                'imagen' => $datos['imagen']
            ]
        );

        //Busca o crear proveedor
        $proveedor = null;

        if ($datos['id_proveedor']) {
            $proveedor = Proveedor::find($datos['id_proveedor']);
        }

        if (!$proveedor && ($datos['nombre_proveedor'] || $datos['email'])) {
            $proveedor = Proveedor::create([
                'nombre' => $datos['nombre_proveedor'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'],
            ]);
        }

        //Actualizar compra si el proveedor cambió
        $detalle = DetalleCompra::with('compra')->find($datos['id_detalle']);

        if ($proveedor && $detalle->compra->id_proveedor !== $proveedor->id) {
            $detalle->compra->id_proveedor = $proveedor->id;
            $detalle->compra->save();
        }

        //Actualiza detalle de compra
        $detalle->update([
            'id_producto' => $producto->id,
            'id_almacen' => $datos['id_almacen'],
            'cantidad_actual' => $datos['cantidad_actual'],
            'precio_unitario' => $datos['precio_unitario'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'],
            'estado' => false
        ]);

        return $this->renderInventario($user);

        
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $datos = $request->validate([
            //producto que pides
            'id_categoria' => 'nullable|integer',
            'codigo' => 'required|string',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'perecedero' => 'nullable|boolean',
            'imagen' => 'nullable|string',

            //Inventario
            'precio_unitario' => 'required|numeric|min:0',
            'cantidad_actual' => 'required|integer|min:1',
            'id_almacen' => 'required|exists:almacenes,id',
            'fecha_vencimiento' => 'nullable|date',

            //Proveedor
            'id_proveedor' => 'nullable',
            'nombre_proveedor' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email',

            //datos para crear la categoria
            'nombre_categoria' => 'nullable|string',
        ]);

        $categoria = Categoria::where('id_user', $user->id)
            ->where(function ($query) use ($datos) {
                $query->where('nombre', $datos['nombre_categoria'])
                    ->orWhere('id', $datos['id_categoria']);
            })
            ->first();

        if(!$categoria){
            $categoria = Categoria::create([
                'id_user'=>$user->id,
                'nombre'=>$datos['nombre_categoria'],
            ]);
        }

        $producto = Producto::where('codigo',$datos['codigo'])
            ->where('id_categoria',$categoria['id'])
            ->where('nombre',$datos['nombre'])
            ->where('descripcion',$datos['descripcion'])
            ->where('imagen',$datos['imagen'])
            ->where('perecedero',$datos['perecedero'])
            ->first();
            

        if(!$producto){
            $producto = Producto::create([
                'id_categoria' => $categoria['id'],
                'codigo' => $datos['codigo'],
                'nombre' =>  $datos['nombre'],
                'descripcion' =>  $datos['descripcion'],
                'perecedero' =>$datos['perecedero'],
                'imagen' =>  $datos['imagen']
            ]);
        }

        $proveedor = null;
        if ($request->filled('id_proveedor')) {
            $proveedor = Proveedor::find($request['id_proveedor']);
        }
        if (!$proveedor) {
            $proveedor = Proveedor::create([
                'nombre' => $request->input('nombre_proveedor'),
                'email' => $request->input('email'),
                'telefono' => $request->input('telefono'),
            ]);
        }

        $almacen = Almacen::where('id_user', $user->id)
        ->where('id',$datos['id_almacen'])
        ->first();

        $compra = Compra::create([
            'id_user' => $user->id,
            'id_proveedor' => $proveedor->id,
            'fecha_compra' => now(),
        ]);

        DetalleCompra::create([
            'fecha_vencimiento'=> $datos['fecha_vencimiento'] ? $datos['fecha_vencimiento'] : null ,
            'id_almacen' => $almacen->id,
            'id_producto' => $producto->id,
            'id_compra' => $compra->id,
            'cantidad_actual' => $datos['cantidad_actual'],
            'precio_unitario' => $datos['precio_unitario'],
            'estado' => false,
        ]);

        return $this->renderInventario($user);
    }

    public function destroy(Request $request){
        $user = Auth::user();
        $datos = $request->validate([
            'id_detalle' => 'required|exists:detalle_compras,id',
        ]);

        $detalle = DetalleCompra::with('compra', 'producto')->find($datos['id_detalle']);

        if ($detalle->compra->id_user !== $user->id) {
            abort(403, 'No tienes permisos para eliminar este detalle.');
        }

        $compra = $detalle->compra;
        $producto = $detalle->producto;

        $detalle->delete();

        if ($compra->detalleCompras()->count() === 0) {
            $compra->delete();
        }

        $productoUsado = DetalleCompra::where('id_producto', $producto->id)->exists() ||
                        Inventario::where('id_producto', $producto->id)->exists();

        if (!$productoUsado) {
            $producto->delete();
        }

        return $this->renderInventario($user);

    }

    public function addInventario(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            //Producto para buscar inventario
            'id_producto' => 'required|numeric',
            'id_almacen' => 'required|numeric',
            'precio_unitario' => 'required|numeric' ,
            'codigo' => 'required|string',
            'id_detalle' => 'required|numeric',
            'fecha_vencimiento' => 'nullable|date',
            'cantidad_actual' =>'required|numeric'
        ]);

        $detalleCompra = DetalleCompra::where('id',$datos['id_detalle'])
            ->first();
        
        $detalleCompra->estado = true;
        $detalleCompra->save();

        $inventario = Inventario::where('id_producto', $datos['id_producto'])
            ->where('precio_unitario', $datos['precio_unitario'])
            ->first();

        if ($inventario) {
            $inventario->cantidad_actual += $datos['cantidad_actual'];
            $inventario->fecha_entrada = now();
            $inventario->save();
        }else{
            $inventario = Inventario::create([
                'id_producto' => $datos['id_producto'],
                'id_almacen'=> $datos['id_almacen'],
                'precio_unitario' => $datos['precio_unitario'],
                'cantidad_actual' => $datos['cantidad_actual'],
                'fecha_entrada' => now(),
                'fecha_salida' => null,
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ? $datos['fecha_vencimiento'] : null,
            ]);
        }
        return $this->renderInventario($user);
    }


    private function renderInventario($user, $almacenesIds = null){

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
                    // He cambiado esto

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

        // Cargar los detalles de compras con sus relaciones para la lista general de proveedores
        $detallesComprasRaw = DetalleCompra::with(['producto', 'compra.proveedor'])
            ->whereHas('compra', function ($query) use ($user) {
                $query->where('id_user', $user->id);
            })
            ->get();

        // Lista general de proveedores únicos (sin repetir)
        $all_proveedores = $detallesComprasRaw
            ->pluck('compra.proveedor')
            ->filter()
            ->unique('id')
            ->values();

        // Mapear detalles de compras y ventas (igual que antes)
        $detallesCompras = $detallesComprasRaw->map(function ($detalle) {
            return [
                'id_almacen' => $detalle->almacen->id,
                'nombre_almacen' => $detalle->almacen->nombre,

                'id_categoria' => $detalle->producto->id_categoria,
                'nombre_categoria' => $detalle->producto->categoria->nombre,

                'id_producto' => $detalle->id_producto,
                'codigo' => $detalle->producto->codigo,
                'nombre' => $detalle->producto->nombre,
                'descripcion' => $detalle->producto->descripcion,
                'imagen' => $detalle->producto->imagen,

                'proveedores' => [$detalle->compra->proveedor],

                'id_detalle' => $detalle->id,
                'fecha_vencimiento'=> $detalle->fecha_vencimiento,
                'cantidad_actual' => $detalle->cantidad_actual,
                'estado' => $detalle->estado,
                'fecha_compra' => optional($detalle->compra)->fecha_compra,
                'precio_unitario' => $detalle->precio_unitario,

            ];
        });

        $detallesVentas = DetalleVenta::with(['producto', 'venta.comprador'])
            ->whereHas('venta', function ($query) use ($user) {
                $query->where('id_user', $user->id);
            })
            ->get()
            ->map(function ($detalle) {
                return [
                    'producto_id' => $detalle->id_producto,
                    'codigo' => $detalle->producto->codigo,
                    'nombre' => $detalle->producto->nombre,
                    'precio_unitario' => $detalle->precio_unitario,
                    'cantidad_actual' => $detalle->cantidad_actual,
                    'fecha_venta' => optional($detalle->venta)->fecha_venta,
                    'cliente' => optional($detalle->venta->comprador)->nombre,
                ];
            });

        $allAlmacenes = Almacen::with(['productos' => function ($query) {
            $query->withPivot('id_almacen', 'cantidad_actual', 'precio_unitario', 'fecha_entrada', 'fecha_salida');
        }])
        ->where('id_user', $user->id)
        ->get();

        $categorias = Categoria::where('id_user', $user->id)
            ->with('productos')
            ->get();

        return Inertia::render('Pedidos', [
            'status' => true,
            'message' => 'Almacenes encontrados',
            'data' => $almacenes,
            'all_productos' => $allProductos,
            'all_almacenes' => $allAlmacenes,
            'all_proveedores' => $all_proveedores,
            'detalles_compras' => $detallesCompras,
            'detalles_ventas' => $detallesVentas,
            'categorias' => $categorias,
        ]);
    }
}
