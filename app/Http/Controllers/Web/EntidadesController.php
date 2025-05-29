<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\DetalleCompra;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EntidadesController extends Controller
{
    //

    public function index(){
        $user = Auth::user();
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
