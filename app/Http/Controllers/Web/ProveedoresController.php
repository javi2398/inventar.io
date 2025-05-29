<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Models\Proveedor;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ProveedoresController extends Controller
{
    //
    public function index(){
        $user = Auth::user();

        return $this->renderProveedores($user);
    }

    public function store(Request $request){
        $user = Auth::user();
        $datos = $request->validate([
            'nombre' => 'required|string|min:1',
            'telefono' => 'required|string|min:9',
            'email' => 'required|email',
        ]);

        Proveedor::create([
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'],
            'email' => $datos['email'],
        ]);

        
        $entidadesController = new EntidadesController();
        return $entidadesController->renderEntidades($user);
    }

    public function destroy (Request $request){

        $user = Auth::user();

        $datos = $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id'
        ]);

        $proveedor = Proveedor::find($datos['id_proveedor']);

        // Verifica si tiene compras asociadas
        $tieneCompras = $proveedor->compras()->where('id_user', $user->id)->exists();

        if ($tieneCompras) {
            return redirect()->back()->with([
                'status' => false,
                'message' => 'No se puede eliminar el proveedor porque tiene compras asociadas.'
            ]);
        }

        $proveedor->delete();

        return (new EntidadesController)->renderEntidades($user);
    }

    public function patch(Request $request){
        $user = Auth::user(); 

        $datos = $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id',
            'nombre' => 'required|string|min:1',
            'telefono' => 'required|string|min:9',
            'email' => 'required|email',
        ]);

        $proveedor = Proveedor::where('id', $datos['id_proveedor'])->first();
        $proveedor->nombre = $datos['nombre'];
        $proveedor->telefono = $datos['telefono'];
        $proveedor->email = $datos['email'];

        $proveedor->save();

        $entidadesController = new EntidadesController();
        return $entidadesController->renderEntidades($user);
    }

    public function renderProveedores($user){
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

        return Inertia::render('Proveedores', [
            'status' => true,
            'mensaje' => 'Proveedores y clientes encontrados',
            'proveedores' => $proveedores,
            'clientes'=> $clientes,
        ]);
    }
}
