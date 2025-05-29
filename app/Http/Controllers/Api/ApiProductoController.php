<?php

namespace App\Http\Controllers\Api;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApiProductoController extends Controller
{
    //
    public function index()
    {
        $data = Producto::all();
        return response()->json(
            [
                'status' => true,
                'message' => 'Listado de productos',
                'count' => count($data),
                'data' => $data
            ],
            200
        );
    }

    public function show(Request $request)
    {
        $producto = Producto::find($request->id);
        if ($producto) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Producto encontrado',
                    'data' => $producto
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Producto no encontrado',
                ],
                404
            );
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio_unitario' => 'required',
        ]);

        $producto = Producto::create($validated);

        $producto->save();
        return response()->json(
            [
                'status' => true,
                'message' => 'Producto creado',
                'data' => $producto
            ],
            201
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio_unitario' => 'required|numeric',
            'id' => 'required|integer',
        ]);

        $producto = Producto::find($request->id);
        if ($producto) {
            $producto->update($validated);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Producto actualizado',
                    'data' => $producto
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Producto no encontrado',
                ],
                404
            );
        }
    }

    public function destroy(Request $request)
    {
        $producto = Producto::find($request->id);
        if ($producto) {
            $producto->delete();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Producto eliminado',
                    'data' => $producto
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Producto no encontrado',
                ],
                404
            );
        }
    }

    public function productos_user()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ],
                401
            );
        }
        
        if ($user->almacenes->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'El usuario no tiene almacenes asignados',
                ],
                404
            );
        }

        $productos = Producto::with(['inventarios' => function ($query) use ($user) {
            $query->whereIn('id_almacen', $user->almacenes->pluck('id')->toArray());
        }])
            ->whereHas('inventarios', function ($query) use ($user) {
                $query->whereIn('id_almacen', $user->almacenes->pluck('id')->toArray());
            })
            ->get();

        $productos_con_inventarios = $productos->map(function ($producto) {
            $inventarios = $producto->inventarios->map(function ($inventario) {
                return [
                    'id_almacen' => $inventario->id_almacen,
                    'cantidad_actual' => $inventario->cantidad_actual,
                    'fecha_entrada' => $inventario->fecha_entrada,
                    'fecha_salida' => $inventario->fecha_salida,
                ];
            });

            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio_unitario' => $producto->precio_unitario,
                'inventarios' => $inventarios, // Incluir inventarios
            ];
        });

        return response()->json(
            [
                'status' => true,
                'message' => 'Productos del usuario: ' . $user->name,
                'count' => $productos_con_inventarios->count(),
                'data' => $productos_con_inventarios
            ],
            200
        );
    }
}
