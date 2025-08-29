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
            
        $stats = [
            'total' => DB::table('categorias')->count(),
            'activas' => DB::table('categorias')->where('activo', true)->count(),
            'comida' => DB::table('categorias')->where('tipo', 'comida')->count(),
            'bebida' => DB::table('categorias')->where('tipo', 'bebida')->count(),
        ];

        return view('categorias.index', [
            'categorias' => $categorias,
            'stats' => $stats
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:comida,bebida,postre,otro',
            'area' => 'required|in:cocina,barra,general',
            'color' => 'required|string',
            'activo' => 'boolean',
        ]);

        DB::table('categorias')->insert([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'tipo' => $validated['tipo'],
            'area' => $validated['area'],
            'color' => $validated['color'],
            'activo' => $validated['activo'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Categoría creada exitosamente']);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:comida,bebida,postre,otro',
            'area' => 'required|in:cocina,barra,general',
            'color' => 'required|string',
            'activo' => 'boolean',
        ]);

        DB::table('categorias')
            ->where('id', $id)
            ->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'tipo' => $validated['tipo'],
                'area' => $validated['area'],
                'color' => $validated['color'],
                'activo' => $validated['activo'] ?? true,
                'updated_at' => now(),
            ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Categoría actualizada exitosamente']);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(Request $request, $id)
    {
        // Verificar si hay productos usando esta categoría
        $productosCount = DB::table('productos')->where('categoria_id', $id)->count();
        
        if ($productosCount > 0) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar la categoría porque tiene productos asociados']);
            }
            return redirect()->back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados');
        }

        DB::table('categorias')->where('id', $id)->delete();
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Categoría eliminada exitosamente']);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada exitosamente');
    }
}
