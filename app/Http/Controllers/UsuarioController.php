<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => DB::table('users')->count(),
            'activos' => DB::table('users')->where('email_verified_at', '!=', null)->count(),
            'nuevos' => DB::table('users')->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'stats' => $stats
        ]);
    }
}
