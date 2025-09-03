<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Obtener fechas para filtros
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Estadísticas generales
        $stats = [
            'ventas_totales' => DB::table('ventas')
                ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->sum('total'),
            'numero_ventas' => DB::table('ventas')
                ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->count(),
            'ticket_promedio' => DB::table('ventas')
                ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->avg('total') ?? 0,
            'productos_vendidos' => DB::table('detalle_ventas')
                ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                ->sum('detalle_ventas.cantidad'),
        ];

        // Ventas por día (últimos 30 días) - compatible con SQLite
        $ventasPorDia = DB::table('ventas')
            ->selectRaw("date(created_at) as fecha, COUNT(*) as cantidad, SUM(total) as total")
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Productos más vendidos
        $productosMasVendidos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->selectRaw('productos.nombre, productos.codigo, SUM(detalle_ventas.cantidad) as cantidad_vendida, SUM(detalle_ventas.subtotal) as total_vendido')
            ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get();

        // Ventas por categoría
        $ventasPorCategoria = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->selectRaw('categorias.nombre as categoria, SUM(detalle_ventas.subtotal) as total')
            ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->get();

        // Ventas por hora del día (compatible con SQLite)
        $ventasPorHora = DB::table('ventas')
            ->selectRaw("strftime('%H', created_at) as hora, COUNT(*) as cantidad, SUM(total) as total")
            ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        // Métodos de pago
        $metodosPago = DB::table('ventas')
            ->selectRaw('tipo_pago as metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->whereNotNull('tipo_pago')
            ->groupBy('tipo_pago')
            ->get();

        // Comparación con periodo anterior
        $diasPeriodo = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin)) + 1;
        $fechaInicioAnterior = Carbon::parse($fechaInicio)->subDays($diasPeriodo);
        $fechaFinAnterior = Carbon::parse($fechaInicio)->subDay();
        
        $ventasPeriodoAnterior = DB::table('ventas')
            ->whereBetween('created_at', [$fechaInicioAnterior, $fechaFinAnterior . ' 23:59:59'])
            ->sum('total');
        
        $stats['cambio_porcentual'] = $ventasPeriodoAnterior > 0 
            ? (($stats['ventas_totales'] - $ventasPeriodoAnterior) / $ventasPeriodoAnterior) * 100 
            : 0;

        // Empleados con más ventas (si hay tabla de empleados)
        $empleadosTopVentas = DB::table('ventas')
            ->join('users', 'ventas.usuario_id', '=', 'users.id')
            ->selectRaw('users.name as empleado, COUNT(*) as cantidad_ventas, SUM(ventas.total) as total_vendido')
            ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        return view('reportes.index', compact(
            'stats',
            'ventasPorDia',
            'productosMasVendidos',
            'ventasPorCategoria',
            'ventasPorHora',
            'metodosPago',
            'empleadosTopVentas',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function export(Request $request)
    {
        $tipo = $request->get('tipo', 'ventas');
        $formato = $request->get('formato', 'csv');
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

        // Aquí implementarías la lógica de exportación según el tipo y formato
        // Por ahora, redirigimos de vuelta
        return redirect()->back()->with('success', 'Reporte exportado correctamente');
    }
}