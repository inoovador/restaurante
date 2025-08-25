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
            ->orderBy('name')
            ->get();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios
        ]);
    }
}
