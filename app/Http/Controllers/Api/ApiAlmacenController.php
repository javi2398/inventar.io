<?php

namespace App\Http\Controllers\Api;

use App\Models\Almacen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

class ApiAlmacenController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();

        $almacenes = Almacen::with('productos')
            ->where('id_user', $user->id)
            ->get();

        if ($almacenes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No hay almacenes disponibles para este usuario',
            ]);
        }

        $totalProductos = $almacenes->sum(function ($almacen) {
            return $almacen->productos->count();
        });

        return response()->json([
            'status' => true,
            'message' => 'Almacenes encontrados',
            'count' => $almacenes->count(),
            'total_productos' => $totalProductos,
            'data' => $almacenes,
        ]);
    }

    public function show(Request $request)
    {

        $user = Auth::user();

        $almacen = Almacen::with('productos')
            ->where('id', $request->id)
            ->where('id_user', $user->id)
            ->first();

        if (!$almacen) {
            return response()->json([
                'status' => false,
                'message' => 'Almacen no encontrado',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Almacen encontrado',
            'productos_totales' => $almacen->productos->count(),
            'data' => $almacen,
        ]);
    }
}
