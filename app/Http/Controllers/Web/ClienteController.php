<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Comprador;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    //

    public function store(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            'nombre' => 'nullable|string|min:1',
            'identificacion' => 'nullable|string|min:1',
            'telefono' => 'nullable|string|min:1',
            'email' => 'nullable|email',
            'direccion' => 'nullable|string|min:1',
            'tipo_comprador' => 'in:particular,empresa',
        ]);

        $comprador = Comprador::create([
            'nombre' => $datos['nombre'],
            'identificacion' =>$datos['identificacion'],
            'telefono' => $datos['telefono'],
            'email' =>$datos['email'] ,
            'direccion' =>$datos['direccion'] ,
            'tipo_comprador' => $datos['tipo_comprador'],
        ]);

        return $this->renderEntidades($user);

    }

    public function destroy(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            'id_cliente' => 'required|exists:compradores,id',
        ]);

        $cliente = Comprador::find($datos['id_cliente']);

        // Verificar si el cliente tiene ventas
        $tieneVentas = $cliente->ventas()->where('id_user', $user->id)->exists();

        if ($tieneVentas) {
            return redirect()->back()->with([
                'status' => false,
                'message' => 'No se puede eliminar el cliente porque tiene ventas registradas.'
            ]);
        }

        $cliente->delete();

        return $this->renderEntidades($user);

    }


    public function patch(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            'id_cliente' => 'required| exists:compradores,id',
            'nombre' => 'nullable|string|min:1',
            'identificacion' => 'nullable|string|min:1',
            'telefono' => 'nullable|string|min:1',
            'email' => 'nullable|email',
            'direccion' => 'nullable|string|min:1',
            'tipo_comprador' => 'in:particular,empresa',
        ]);

        $cliente = Comprador::where('id',$datos['id_cliente'])->first();

        $cliente->nombre = $datos['nombre'];
        $cliente->identificacion = $datos['identificacion'];
        $cliente->telefono = $datos['telefono'];
        $cliente->email = $datos['email'];
        $cliente->direccion = $datos['direccion'];
        $cliente->tipo_comprador = $datos['tipo_comprador'];

        $cliente->save();

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
