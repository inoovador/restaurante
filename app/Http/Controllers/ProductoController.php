<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*', 'categorias.nombre as categoria_nombre', 'categorias.color as categoria_color')
            ->orderBy('productos.nombre')
            ->get();

        $categorias = DB::table('categorias')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Productos/Index', [
            'productos' => $productos,
            'categorias' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:productos',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        DB::table('productos')->insert([
            'codigo' => $validated['codigo'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'categoria_id' => $validated['categoria_id'],
            'precio_venta' => $validated['precio_venta'],
            'precio_compra' => $validated['precio_compra'],
            'stock' => $validated['stock'],
            'stock_minimo' => $validated['stock_minimo'],
            'unidad' => 'unidad',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);

        DB::table('productos')
            ->where('id', $id)
            ->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'categoria_id' => $validated['categoria_id'],
                'precio_venta' => $validated['precio_venta'],
                'precio_compra' => $validated['precio_compra'],
                'stock' => $validated['stock'],
                'stock_minimo' => $validated['stock_minimo'],
                'activo' => $validated['activo'] ?? true,
                'updated_at' => now(),
            ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($id)
    {
        DB::table('productos')->where('id', $id)->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente');
    }
}