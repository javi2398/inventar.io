<?php

namespace App\Http\Controllers\Web;

use Exception;
use Inertia\Inertia;
use App\Models\Gasto;
use App\Models\Venta;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $userId = $user->id;

        $fechaInicio = now()->startOfMonth()->toDateTimeString();
        $fechaFin = now()->endOfMonth()->toDateTimeString();

        $fechaInicioAnterior = now()->subMonth()->startOfMonth()->toDateTimeString();
        $fechaFinAnterior = now()->subMonth()->endOfMonth()->toDateTimeString();

        try {
            
            // No tienes histórico, ponemos 0 para anterior
            $totalAlmacenesAnterior = 0;

            $almacenes = Almacen::with('productos')->where('id_user', $userId)->get();
            // Total almacenes (actual y anterior)

            // Obtener el total de productos distintos en todos los almacenes del usuario
            $totalProductos = $almacenes->pluck('productos')
            ->flatten() // Unir todas las colecciones de productos
            ->unique('id') // Evitar contar productos duplicados entre almacenes
            ->count();
            
            $totalAlmacenes = $almacenes->count();
            
            $totalProductosAnterior = 0;

            // Total ventas (actual y anterior)
            $totalVentas = DetalleVenta::join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->where('ventas.id_user', $userId)
                ->whereBetween('ventas.fecha_venta', [$fechaInicio, $fechaFin])
                ->select(DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as total'))
                ->value('total') ?? 0;

            $totalCompras = DetalleCompra::whereHas('compra', function ($query) use ($userId, $fechaInicio, $fechaFin) {
                    $query->where('id_user', $userId)
                        ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin]);
                })
                ->selectRaw('SUM(cantidad_actual * precio_unitario) as total')
                ->value('total') ?? 0;

            $totalComprasAnterior = DetalleCompra::whereHas('compra', function ($query) use ($userId, $fechaInicioAnterior, $fechaFinAnterior) {
                $query->where('id_user', $userId)
                    ->whereBetween('fecha_compra', [$fechaInicioAnterior, $fechaFinAnterior]);
                })
                ->selectRaw('SUM(cantidad_actual * precio_unitario) as total')
                ->value('total') ?? 0;

            $totalVentasAnterior = DetalleVenta::join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->where('ventas.id_user', $userId)
                ->whereBetween('ventas.fecha_venta', [$fechaInicioAnterior, $fechaFinAnterior])
                ->select(DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as total'))
                ->value('total') ?? 0;

            // Total gastos (actual y anterior)
            $totalGastos = Gasto::where('id_user', $userId)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('precio');

            $totalGastosAnterior = Gasto::where('id_user', $userId)
                ->whereBetween('fecha', [$fechaInicioAnterior, $fechaFinAnterior])
                ->sum('precio');

            // Beneficio mensual (actual y anterior)
            $beneficioMensual = $totalVentas - ($totalGastos + $totalCompras);
            $beneficioMensualAnterior = $totalVentasAnterior - ($totalGastosAnterior + $totalComprasAnterior);

            // Función para calcular crecimiento en porcentaje (ventas, beneficio)
            $calcGrowthPercent = function ($current, $previous) {
                if ($previous == 0) {
                    if ($current == 0) {
                        return '0%';
                    }
                    return 'N/A';
                }
                $growth = (($current - $previous) / abs($previous)) * 100;
                return ($growth >= 0 ? '+' : '') . round($growth, 1) . '%';
            };

            // Función para crecimiento absoluto (almacenes, productos)
            $calcGrowthAbsolute = function ($current, $previous) {
                $diff = $current - $previous;
                if ($previous == 0) {
                    if ($current == 0) return '0';
                    return 'Nuevo';
                }
                return ($diff > 0 ? '+' : '') . $diff;
            };


            // Producto estrella
            $productoEstrella = Producto::select('productos.id', 'productos.nombre', 'productos.imagen', 'productos.descripcion', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
                ->join('detalle_ventas', 'productos.id', '=', 'detalle_ventas.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->where('ventas.id_user', $userId)
                ->whereBetween('ventas.fecha_venta', [$fechaInicio, $fechaFin])
                ->groupBy('productos.id', 'productos.nombre', 'productos.imagen', 'productos.descripcion')
                ->orderByDesc('total_vendido')
                ->first();

            // Ventas última semana
            $fechaInicioSemana = now()->subDays(6)->startOfDay()->toDateTimeString();
            $fechaFinSemana = now()->endOfDay()->toDateTimeString();

            $ventasUltimaSemana = DetalleVenta::join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->where('ventas.id_user', $userId)
                ->whereBetween('ventas.fecha_venta', [$fechaInicioSemana, $fechaFinSemana])
                ->select(
                    DB::raw('DATE(ventas.fecha_venta) as fecha'),
                    DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as total')
                )
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();
            
            $fechasSemana = collect();
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $dia = \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('ddd'); // "lun", "mar", etc.
                $fechasSemana->push([
                    'fecha' => $fecha,
                    'dia' => ucfirst($dia),
                    'total' => 0
                ]);
            }

            // Combinar con ventas reales
            $ventasAgrupadas = $ventasUltimaSemana->keyBy('fecha');

            $ventasConDias = $fechasSemana->map(function ($dia) use ($ventasAgrupadas) {
                $venta = $ventasAgrupadas->get($dia['fecha']);
                return [
                    'dia' => $dia['dia'],
                    'total' => $venta ? round($venta->total, 2) : 0,
                ];
            });

            // Gastos últimos 5 meses
            $fechaCincoMesesAntes = now()->subMonths(4)->startOfMonth()->toDateString();

            $gastosMensuales = Gasto::where('id_user', $userId)
                ->where('fecha', '>=', $fechaCincoMesesAntes)
                ->select(
                    DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'),
                    DB::raw('SUM(precio) as total_gastos')
                )
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();

            // Distribución ventas por categoría
            $distribucionVentas = Producto::select('categorias.nombre as categoria', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
                ->join('categorias', 'productos.id_categoria', '=', 'categorias.id')
                ->join('detalle_ventas', 'productos.id', '=', 'detalle_ventas.id_producto')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->where('ventas.id_user', $userId)
                ->whereBetween('ventas.fecha_venta', [$fechaInicio, $fechaFin])
                ->groupBy('categorias.nombre')
                ->orderByDesc('total_vendido')
                ->get();



            return Inertia::render('Dashboard', [
                'total_ventas' => round($totalVentas, 2), 
                'total_compras' => round($totalCompras, 2),
                'total_gastos' => round($totalGastos, 2),
                'total_almacenes' => $totalAlmacenes,
                'total_productos' => $totalProductos,
                'beneficio_mensual' => round($beneficioMensual, 2),
                'producto_estrella' => $productoEstrella,
                'ventas_ultima_semana' => $ventasUltimaSemana,
                'gastos_mensuales' => $gastosMensuales,
                'distribucion_ventas' => $distribucionVentas,
                'ventas_dias'=>$ventasConDias
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['dashboard' => 'No se pudo cargar el dashboard.']);

        }
    }
}
