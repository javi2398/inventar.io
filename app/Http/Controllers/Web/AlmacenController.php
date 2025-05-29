<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\{Almacen, Categoria, Inventario, DetalleCompra};

class AlmacenController extends Controller
{
    public function index()
    {
        return $this->renderInventario(Auth::user());
    }

    public function store(Request $request)
    {
        $data = $this->validateAlmacen($request);

        Almacen::create([
            'id_user' => Auth::id(),
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
        ]);

        return $this->renderInventario(Auth::user());
    }

    public function storeEntidades(Request $request)
    {
        $user = Auth::user();
        $data = $this->validateAlmacen($request);

        Almacen::create([
            'id_user' => Auth::id(),
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
        ]);

        $entidadesController = new EntidadesController();
        return $entidadesController->renderEntidades($user);
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'id' => 'required|integer', 
        ]);

        $almacen = $this->findUserAlmacen($data['id']);

        $tieneCompras = DetalleCompra::where('id_almacen', $almacen->id)->exists();
        $tieneVentas = DetalleVenta::where('id_almacen', $almacen->id)->exists();

        if ($tieneCompras || $tieneVentas) {
            return redirect()->back()->with([
                'status' => false,
                'message' => 'No se puede eliminar el almacén porque tiene registros de compras o ventas asociados.'
            ]);
        }

        Inventario::where('id_almacen', $almacen->id)->delete();
        $almacen->delete();

        return $this->renderInventario($user);
    }

    public function deleteEntidades(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'id' => 'required|integer', 
        ]);

        $almacen = $this->findUserAlmacen($data['id']);

        $tieneCompras = DetalleCompra::where('id_almacen', $almacen->id)->exists();
        $tieneVentas = DetalleVenta::where('id_almacen', $almacen->id)->exists();

        if ($tieneCompras || $tieneVentas) {
            return redirect()->back()->with([
                'status' => false,
                'message' => 'No se puede eliminar el almacén porque tiene registros de compras o ventas asociados.'
            ]);
        }

        Inventario::where('id_almacen', $almacen->id)->delete();
        $almacen->delete();

        $entidadesController = new EntidadesController();
        return $entidadesController->renderEntidades($user);
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $datos = $request->validate([
            'id_almacen' => 'nullable|exists:almacenes,id',
            'nombre' => 'required|string|min:1',
            'direccion' => 'required|string|min:1'
        ]);
        
        $almacen = Almacen::where('id_user',$user->id)
        ->where('id_almacen',$datos['id_almacen'])
        ->first();

        $almacen->nombre = $datos['nombre'];
        $almacen->direccion = $datos['direccion'];
        $almacen->save();

        $entidadesController = new EntidadesController();
        return $entidadesController->renderEntidades($user); 
    }

    

    private function validateAlmacen(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
        ]);
    }

    private function findUserAlmacen($id)
    {
        return Almacen::where('id_user', Auth::id())
            ->where('id', $id)
            ->first();
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
