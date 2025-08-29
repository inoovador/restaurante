<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        $caja = DB::table('cajas')
            ->where('estado', 'abierta')
            ->orderBy('id', 'desc')
            ->first();

        $movimientos = DB::table('movimientos_caja')
            ->where('fecha', '>=', now()->startOfDay())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $stats = [
            'saldo_inicial' => $caja->saldo_inicial ?? 0,
            'ingresos' => DB::table('movimientos_caja')->where('tipo', 'ingreso')->where('fecha', '>=', now()->startOfDay())->sum('monto') ?? 0,
            'egresos' => DB::table('movimientos_caja')->where('tipo', 'egreso')->where('fecha', '>=', now()->startOfDay())->sum('monto') ?? 0,
            'ventas_dia' => DB::table('ventas')->where('fecha', '>=', now()->startOfDay())->sum('total') ?? 0,
        ];
        
        $stats['saldo_actual'] = $stats['saldo_inicial'] + $stats['ingresos'] - $stats['egresos'];

        return view('caja.index', [
            'caja' => $caja,
            'movimientos' => $movimientos,
            'stats' => $stats
        ]);
    }
}
