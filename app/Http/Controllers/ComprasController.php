<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ComprasController extends Controller
{
    public function index()
    {
        $compras = DB::table('compras')
            ->leftJoin('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
            ->select(
                'compras.*',
                'proveedores.nombre as proveedor_nombre',
                'proveedores.ruc as proveedor_ruc'
            )
            ->orderBy('compras.fecha', 'desc')
            ->limit(50)
            ->get();

        $proveedores = DB::table('proveedores')
            ->select('id', 'nombre', 'ruc', 'telefono')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $stats = [
            'total' => DB::table('compras')->count(),
            'mes_actual' => DB::table('compras')->whereMonth('fecha', now()->month)->count(),
            'total_monto' => DB::table('compras')->whereMonth('fecha', now()->month)->sum('total') ?? 0,
            'pendientes' => DB::table('compras')->where('estado', 'pendiente')->count(),
        ];

        return view('compras.index', [
            'compras' => $compras,
            'proveedores' => $proveedores,
            'stats' => $stats
        ]);
    }
}