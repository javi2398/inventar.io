<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use Illuminate\Support\Facades\Auth;

class CategoriasController extends Controller
{
    //  

    public function store(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            'nombre' => 'required|string|min:1',
        ]);

        Categoria::create([
            'id_user' => $user->id,
            'nombre' => $datos['nombre'],
        ]);

        return $this->renderEntidades($user);

    }   

    public function destroy (Request $request){

        $user = Auth::user();

        $datos = $request->validate([
            'id_categoria' => 'required|exists:categorias,id'
        ]);

        $categoria = Categoria::where('id', $datos['id_categoria'])
            ->where('id_user', $user->id)
            ->withCount('productos')
            ->first();

        if ($categoria->productos_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos relacionados.'
            ], 422);
        }

        $categoria->delete();

        return $this->renderEntidades($user);

    }

    public function patch(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            'id_categoria' => 'required|exists:categorias,id',
            'nombre' => 'required|string|min:1'
        ]);

        $categoria = Categoria::where('id_user',$user->id)
            ->where('nombre',$datos['nombre'])
            ->first();

        $categoria->nombre = $datos['nombre'];
        $categoria->save();

        return $this->renderEntidades($user);
    }

    public function renderEntidades($user){
    
        $categorias = Categoria::where('id_user', $user->id)->with('productos')->get();
        $almacenes = Almacen::where('id_user', $user->id)->get();

        // CLIENTES RELACIONADOS CON LAS VENTAS REALIZADAS
        $detallesVentasRaw = DetalleVenta::with(['producto', 'venta.comprador'])
            ->whereHas('venta', function ($query) use ($user) {
                $query->where('id_user', $user->id);
            })
            ->get();

        $clientes = $detallesVentasRaw
            ->pluck('venta.comprador') 
            ->filter() 
            ->unique('id') 
            ->values();

        // PROVEEDORES RELACIONADOS CON PEDIDOS DEL USUARIO
        $detallesComprasRaw = DetalleCompra::with(['producto', 'compra.proveedor'])
            ->whereHas('compra', function ($query) use ($user){
                $query->where('id_user', $user->id);
            })
            ->get();

        $proveedores = $detallesComprasRaw
            ->pluck('compra.proveedor')
            ->filter()
            ->unique('id')
            ->values();
            
        return Inertia::render('Entidades' , [
            'all_almacenes' => $almacenes,
            'all_categorias' => $categorias,
            'all_clientes' => $clientes,
            'all_proveedores' => $proveedores,
        ]);
    }
}
