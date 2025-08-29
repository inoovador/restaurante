<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = DB::table('clientes')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $stats = [
            'total' => DB::table('clientes')->count(),
            'nuevos' => DB::table('clientes')->where('created_at', '>=', now()->subDays(7))->count(),
            'frecuentes' => DB::table('clientes')->where('visitas', '>=', 5)->count(),
        ];

        return view('clientes.index', [
            'clientes' => $clientes,
            'stats' => $stats
        ]);
    }
}
