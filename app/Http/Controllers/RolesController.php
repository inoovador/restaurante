<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        $roles = DB::table('roles')
            ->select('id', 'nombre', 'descripcion', 'activo')
            ->orderBy('nombre')
            ->get();

        $permisos = [
            'ventas' => 'Gestión de Ventas',
            'productos' => 'Gestión de Productos',
            'clientes' => 'Gestión de Clientes',
            'usuarios' => 'Gestión de Usuarios',
            'reportes' => 'Ver Reportes',
            'caja' => 'Control de Caja',
            'inventario' => 'Control de Inventario'
        ];

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'permisos' => $permisos
        ]);
    }
}