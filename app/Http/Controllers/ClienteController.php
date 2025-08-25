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
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes
        ]);
    }
}
