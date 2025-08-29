<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class BarraController extends Controller
{
    public function index()
    {
        $pedidosPendientes = DB::table('ventas_detalle')
            ->join('ventas', 'ventas_detalle.venta_id', '=', 'ventas.id')
            ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
            ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('ventas_detalle.estado_cocina', 'pendiente')
            ->where('categorias.area', 'barra')
            ->select(
                'ventas_detalle.*',
                'productos.nombre as producto_nombre',
                'productos.tiempo_preparacion',
                'categorias.tipo as categoria_tipo',
                'ventas.id as venta_id',
                'ventas.tipo_pedido',
                'ventas.created_at as hora_pedido',
                'mesas.numero as mesa_numero',
                'mesas.zona as mesa_zona'
            )
            ->orderBy('ventas.created_at', 'asc')
            ->get();

        $bebidas = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('categorias.area', 'barra')
            ->where('productos.activo', true)
            ->select('productos.*', 'categorias.nombre as categoria_nombre', 'categorias.tipo')
            ->orderBy('categorias.nombre')
            ->orderBy('productos.nombre')
            ->get();

        $stats = [
            'pendientes' => $pedidosPendientes->count(),
            'en_preparacion' => DB::table('ventas_detalle')
                ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->where('ventas_detalle.estado_cocina', 'preparando')
                ->where('categorias.area', 'barra')
                ->count(),
            'completados_hoy' => DB::table('ventas_detalle')
                ->join('ventas', 'ventas_detalle.venta_id', '=', 'ventas.id')
                ->join('productos', 'ventas_detalle.producto_id', '=', 'productos.id')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->where('ventas_detalle.estado_cocina', 'listo')
                ->where('categorias.area', 'barra')
                ->whereDate('ventas.created_at', now()->toDateString())
                ->count(),
        ];

        return view('barra.index', [
            'pedidosPendientes' => $pedidosPendientes,
            'bebidas' => $bebidas,
            'stats' => $stats
        ]);
    }
}