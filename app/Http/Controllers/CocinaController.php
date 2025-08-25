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

        return Inertia::render('Cocina/Index', [
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosEnPreparacion' => $pedidosEnPreparacion
        ]);
    }
}