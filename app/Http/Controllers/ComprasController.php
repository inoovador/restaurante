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

        return Inertia::render('Compras/Index', [
            'compras' => $compras,
            'proveedores' => $proveedores
        ]);
    }
}