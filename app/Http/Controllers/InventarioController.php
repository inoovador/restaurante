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
            ->select('id', 'codigo', 'nombre', 'stock', 'stock_minimo')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Inventario/Index', [
            'productos' => $productos,
            'movimientos' => []
        ]);
    }
}
