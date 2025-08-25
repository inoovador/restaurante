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

        return Inertia::render('Caja/Index', [
            'caja' => $caja,
            'movimientos' => []
        ]);
    }
}
