<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $productos = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('productos.activo', true)
            ->where('productos.stock', '>', 0)
            ->select('productos.*', 'categorias.nombre as categoria_nombre', 'categorias.tipo as categoria_tipo')
            ->orderBy('categorias.nombre')
            ->orderBy('productos.nombre')
            ->get();

        $categorias = DB::table('categorias')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $mesas = DB::table('mesas')
            ->where('activo', true)
            ->where('estado', 'disponible')
            ->orderBy('numero')
            ->get();

        $clientes = DB::table('clientes')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Ventas/POS', [
            'productos' => $productos,
            'categorias' => $categorias,
            'mesas' => $mesas,
            'clientes' => $clientes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'mesa_id' => 'nullable|exists:mesas,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'tipo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Crear venta
            $numero_venta = 'V' . date('YmdHis');
            $venta_id = DB::table('ventas')->insertGetId([
                'numero_venta' => $numero_venta,
                'cliente_id' => $validated['cliente_id'],
                'mesa_id' => $validated['mesa_id'],
                'usuario_id' => auth()->id(),
                'subtotal' => $validated['subtotal'],
                'impuesto' => 0,
                'descuento' => 0,
                'total' => $validated['total'],
                'estado' => 'pagado',
                'tipo_pago' => $validated['tipo_pago'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insertar detalles y actualizar stock
            foreach ($validated['items'] as $item) {
                DB::table('detalle_ventas')->insert([
                    'venta_id' => $venta_id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                    'estado' => 'entregado',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Actualizar stock
                DB::table('productos')
                    ->where('id', $item['producto_id'])
                    ->decrement('stock', $item['cantidad']);
            }

            // Si hay mesa, cambiar estado a ocupada
            if ($validated['mesa_id']) {
                DB::table('mesas')
                    ->where('id', $validated['mesa_id'])
                    ->update(['estado' => 'ocupada']);
            }

            DB::commit();
            
            return redirect()->route('ventas.index')->with('success', 'Venta realizada exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al procesar la venta');
        }
    }
}