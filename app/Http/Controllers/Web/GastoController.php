<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Models\Gasto;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{

    public function create(Request $request)
    {
        $user = Auth::user();
        
        $datos = $request->validate([
            'concepto' => 'required|string|min:1',
            'precio' => 'required|numeric',
            'fecha' => 'required|date',
            'gasto_recurrente'=> 'required|boolean'
        ]);

        $gasto = Gasto::create([
            'id_user'=> $user->id,
            'concepto' => $datos['concepto'],
            'precio' => $datos['precio'] ,
            'fecha' => $datos['fecha'],
            'gasto_recurrente'=> $datos['gasto_recurrente']
        ]);

        return $this->renderEntidades($user);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $datos = $request->validate([
            'id_gasto' => 'required|exists:gasto,id',
        ]);

        $gasto = Gasto::where('id',$datos['id_gasto'])
            ->where('id_user',$user->id)->first();

        $gasto->delete();

        return $this->renderEntidades($user);
    }

    public function patch(Request $request){
        $user = Auth::user();

        $datos = $request->validate([
            'id_gasto' => 'required|exists:gastos,id',
            'concepto' => 'required|string',
            'precio' => 'required|numeric',
            'fecha' => 'required|date',
            'gasto_recurrente'=> 'required|boolean'
        ]);
        
        $gasto = Gasto::where('id', $datos['id_gasto'])->first();
        $gasto->concepto = $datos['concepto'];
        $gasto-> precio = $datos['precio'];
        $gasto->fecha = $datos['fecha'];
        $gasto->gasto_recurrente = $datos['gasto_recurrente'];
        $gasto->save();

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
