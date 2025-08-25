<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'categorias' => DB::table('categorias')->count(),
            'productos' => DB::table('productos')->count(),
            'mesas' => DB::table('mesas')->count(),
            'clientes' => DB::table('clientes')->count(),
            'ventas_hoy' => DB::table('ventas')
                ->whereDate('created_at', today())
                ->sum('total') ?? 0,
            'mesas_disponibles' => DB::table('mesas')
                ->where('estado', 'disponible')
                ->count(),
        ];

        $categorias = DB::table('categorias')
            ->select('id', 'nombre', 'tipo', 'area', 'color')
            ->where('activo', true)
            ->get();

        $productos_recientes = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*', 'categorias.nombre as categoria_nombre')
            ->orderBy('productos.created_at', 'desc')
            ->limit(5)
            ->get();

        $mesas = DB::table('mesas')
            ->select('numero', 'capacidad', 'estado', 'zona')
            ->where('activo', true)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'categorias' => $categorias,
            'productos_recientes' => $productos_recientes,
            'mesas' => $mesas,
        ]);
    }
}