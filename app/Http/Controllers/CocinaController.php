<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CocinaController extends Controller
{
    public function index()
    {
        $pedidosPendientes = DB::table('ventas_detalle')
            ->join('ventas', 'ventas_detalle.venta_id', '=', 'ventas.id')
            ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
            ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('ventas_detalle.estado_cocina', 'pendiente')
            ->where('categorias.area', 'cocina')
            ->select(
                'ventas_detalle.*',
                'productos.nombre as producto_nombre',
                'productos.tiempo_preparacion',
                'ventas.id as venta_id',
                'ventas.tipo_pedido',
                'ventas.created_at as hora_pedido',
                'mesas.numero as mesa_numero',
                'mesas.zona as mesa_zona'
            )
            ->orderBy('ventas.created_at', 'asc')
            ->get();

        $pedidosEnPreparacion = DB::table('ventas_detalle')
            ->join('ventas', 'ventas_detalle.venta_id', '=', 'ventas.id')
            ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
            ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('ventas_detalle.estado_cocina', 'preparando')
            ->where('categorias.area', 'cocina')
            ->select(
                'ventas_detalle.*',
                'productos.nombre as producto_nombre',
                'productos.tiempo_preparacion',
                'ventas.id as venta_id',
                'ventas.tipo_pedido',
                'ventas.created_at as hora_pedido',
                'mesas.numero as mesa_numero',
                'mesas.zona as mesa_zona'
            )
            ->orderBy('ventas.created_at', 'asc')
            ->get();

        $stats = [
            'pendientes' => $pedidosPendientes->count(),
            'en_preparacion' => $pedidosEnPreparacion->count(),
            'listos' => DB::table('ventas_detalle')
                ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->where('ventas_detalle.estado_cocina', 'listo')
                ->where('categorias.area', 'cocina')
                ->whereDate('ventas_detalle.updated_at', now()->toDateString())
                ->count(),
            'completados_hoy' => DB::table('ventas_detalle')
                ->join('ventas', 'ventas_detalle.venta_id', '=', 'ventas.id')
                ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->where('ventas_detalle.estado_cocina', 'entregado')
                ->where('categorias.area', 'cocina')
                ->whereDate('ventas.created_at', now()->toDateString())
                ->count(),
        ];

        return view('cocina.index', [
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosEnPreparacion' => $pedidosEnPreparacion,
            'stats' => $stats
        ]);
    }
}