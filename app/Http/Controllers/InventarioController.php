<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        $productos = DB::table('productos')
            ->select('id', 'codigo', 'nombre', 'stock', 'stock_minimo', 'precio_compra', 'activo')
            ->orderBy('nombre')
            ->get();

        $movimientos = DB::table('movimientos_inventario')
            ->leftJoin('productos', 'movimientos_inventario.producto_id', '=', 'productos.id')
            ->select(
                'movimientos_inventario.*',
                'productos.nombre as producto_nombre',
                'productos.codigo as producto_codigo'
            )
            ->orderBy('movimientos_inventario.created_at', 'desc')
            ->limit(50)
            ->get();

        $stats = [
            'total_productos' => $productos->count(),
            'stock_bajo' => $productos->filter(function($p) { return $p->stock <= $p->stock_minimo; })->count(),
            'valor_inventario' => $productos->sum(function($p) { return $p->stock * ($p->precio_compra ?? 0); }),
            'productos_activos' => $productos->where('activo', true)->count(),
        ];

        return view('inventario.index', [
            'productos' => $productos,
            'movimientos' => $movimientos,
            'stats' => $stats
        ]);
    }
}
