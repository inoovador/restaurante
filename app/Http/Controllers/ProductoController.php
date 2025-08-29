<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        $stats = [
            'total' => DB::table('productos')->count(),
            'activos' => DB::table('productos')->where('activo', true)->count(),
            'stock_bajo' => DB::table('productos')->whereColumn('stock', '<=', 'stock_minimo')->count(),
            'sin_stock' => DB::table('productos')->where('stock', 0)->count(),
        ];

        return view('productos.index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'stats' => $stats
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
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'activo' => 'boolean',
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $filename = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('uploads/productos'), $filename);
            $imagenPath = 'uploads/productos/' . $filename;
        }

        DB::table('productos')->insert([
            'codigo' => $validated['codigo'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'categoria_id' => $validated['categoria_id'],
            'precio_venta' => $validated['precio_venta'],
            'precio_compra' => $validated['precio_venta'] * 0.7,
            'stock' => $validated['stock'],
            'stock_minimo' => 10,
            'unidad' => 'unidad',
            'imagen' => $imagenPath,
            'activo' => $validated['activo'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Producto creado exitosamente']);
        }
        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'codigo' => 'required|string',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'activo' => 'boolean',
        ]);

        $updateData = [
            'codigo' => $validated['codigo'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'categoria_id' => $validated['categoria_id'],
            'precio_venta' => $validated['precio_venta'],
            'precio_compra' => $validated['precio_venta'] * 0.7,
            'stock' => $validated['stock'],
            'stock_minimo' => 10,
            'activo' => $validated['activo'] ?? true,
            'updated_at' => now(),
        ];

        // Manejar la imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            $producto = DB::table('productos')->where('id', $id)->first();
            if ($producto && $producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }
            
            // Subir nueva imagen
            $imagen = $request->file('imagen');
            $filename = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('uploads/productos'), $filename);
            $updateData['imagen'] = 'uploads/productos/' . $filename;
        }

        DB::table('productos')->where('id', $id)->update($updateData);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente']);
        }
        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($id)
    {
        DB::table('productos')->where('id', $id)->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        }
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente');
    }

    public function updateImage(Request $request, $id)
    {
        $validated = $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $producto = DB::table('productos')->where('id', $id)->first();
        
        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        // Eliminar imagen anterior si existe
        if ($producto->imagen && file_exists(public_path($producto->imagen))) {
            unlink(public_path($producto->imagen));
        }
        
        // Subir nueva imagen
        $imagen = $request->file('imagen');
        $filename = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
        $imagen->move(public_path('uploads/productos'), $filename);
        $imagenPath = 'uploads/productos/' . $filename;

        // Actualizar en la base de datos
        DB::table('productos')->where('id', $id)->update([
            'imagen' => $imagenPath,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Imagen actualizada exitosamente']);
    }
}