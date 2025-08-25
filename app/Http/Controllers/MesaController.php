<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = DB::table('mesas')
            ->orderBy('numero')
            ->get();

        return Inertia::render('Mesas/Index', [
            'mesas' => $mesas
        ]);
    }

    public function updateEstado(Request $request, $id)
    {
        $validated = $request->validate([
            'estado' => 'required|in:disponible,ocupada,reservada,mantenimiento',
        ]);

        DB::table('mesas')
            ->where('id', $id)
            ->update([
                'estado' => $validated['estado'],
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Estado de mesa actualizado');
    }
}