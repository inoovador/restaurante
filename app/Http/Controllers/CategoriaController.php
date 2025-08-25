<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = DB::table('categorias')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias
        ]);
    }
}
