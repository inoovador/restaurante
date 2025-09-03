<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        // Obtener pedidos (ventas) con sus detalles
        $pedidos = DB::table('ventas')
            ->leftJoin('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
            ->select(
                'ventas.*',
                'clientes.nombre as cliente_nombre',
                'clientes.telefono as cliente_telefono',
                'mesas.numero as mesa_numero'
            )
            ->orderBy('ventas.created_at', 'desc')
            ->limit(50)
            ->get();

        // Obtener detalles de cada pedido
        foreach ($pedidos as $pedido) {
            $pedido->items = DB::table('detalle_ventas')
                ->leftJoin('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->where('detalle_ventas.venta_id', $pedido->id)
                ->select(
                    'detalle_ventas.*',
                    'productos.nombre as producto_nombre',
                    'productos.imagen'
                )
                ->get();
        }

        // Estadísticas de pedidos
        $stats = [
            'total_pedidos' => $pedidos->count(),
            'pedidos_pendientes' => $pedidos->where('estado', 'pendiente')->count(),
            'pedidos_preparando' => $pedidos->where('estado', 'preparando')->count(),
            'pedidos_completados' => $pedidos->where('estado', 'completado')->count(),
        ];

        return view('pedidos.index', [
            'pedidos' => $pedidos,
            'stats' => $stats
        ]);
    }

    public function show(Request $request, $id)
    {
        $pedido = DB::table('ventas')
            ->leftJoin('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
            ->where('ventas.id', $id)
            ->select(
                'ventas.*',
                'clientes.nombre as cliente_nombre',
                'clientes.telefono as cliente_telefono',
                'mesas.numero as mesa_numero'
            )
            ->first();

        if (!$pedido) {
            abort(404);
        }

        $pedido->items = DB::table('detalle_ventas')
            ->leftJoin('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('detalle_ventas.venta_id', $pedido->id)
            ->select(
                'detalle_ventas.*',
                'productos.nombre as producto_nombre',
                'productos.imagen'
            )
            ->get();

        return view('pedidos.show', compact('pedido'));
    }

    public function print($id)
    {
        $pedido = DB::table('ventas')
            ->leftJoin('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
            ->where('ventas.id', $id)
            ->select(
                'ventas.*',
                'clientes.nombre as cliente_nombre',
                'clientes.telefono as cliente_telefono',
                'mesas.numero as mesa_numero'
            )
            ->first();

        if (!$pedido) {
            abort(404);
        }

        $pedido->items = DB::table('detalle_ventas')
            ->leftJoin('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('detalle_ventas.venta_id', $pedido->id)
            ->select(
                'detalle_ventas.*',
                'productos.nombre as producto_nombre',
                'productos.imagen'
            )
            ->get();

        return view('pedidos.print', compact('pedido'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,preparando,listo,completado,cancelado'
        ]);

        DB::table('ventas')
            ->where('id', $id)
            ->update([
                'estado' => $request->estado,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }
}
