<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\DetalleVenta;

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
            ->where('estado', 'disponible')
            ->where('activo', true)
            ->orderBy('numero')
            ->get();

        $clientes = DB::table('clientes')
            ->orderBy('nombre')
            ->get();

        $ventas_hoy = DB::table('ventas')
            ->whereDate('created_at', now()->toDateString())
            ->get();

        $stats = [
            'ventas_hoy' => $ventas_hoy->count(),
            'total_ventas' => $ventas_hoy->sum('total'),
            'productos_vendidos' => DB::table('detalle_ventas')
                ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                ->whereDate('ventas.created_at', now()->toDateString())
                ->sum('detalle_ventas.cantidad'),
            'ticket_promedio' => $ventas_hoy->count() > 0 ? $ventas_hoy->avg('total') : 0
        ];

        return view('ventas.index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'mesas' => $mesas,
            'clientes' => $clientes,
            'stats' => $stats
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'mesa_id' => 'nullable|exists:mesas,id',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio' => 'required|numeric|min:0',
                'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
                'subtotal' => 'required|numeric|min:0',
                'iva' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . json_encode($e->errors())
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Validación SUNAT: Para ventas >= S/. 700 se requiere identificación del cliente
        if ($validated['total'] >= 700 && empty($validated['cliente_id'])) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'NORMATIVA SUNAT: Para ventas de S/. 700 o más es obligatorio registrar la identificación del cliente.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Para ventas de S/. 700 o más es obligatorio registrar la identificación del cliente (Normativa SUNAT).')->withInput();
        }
        
        // Validación: IGV debe ser 18% del subtotal
        $igv_calculado = round($validated['subtotal'] * 0.18, 2);
        if (abs($validated['iva'] - $igv_calculado) > 0.01) { // Tolerancia de 1 céntimo
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error en cálculo de IGV. Esperado: S/. {$igv_calculado}, Recibido: S/. {$validated['iva']}"
                ], 422);
            }
            return redirect()->back()->with('error', "Error en cálculo de IGV")->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Crear venta
            $numero_venta = 'V' . date('YmdHis');
            
            // Verificar si hay un usuario autenticado, si no usar ID 1
            $usuario_id = 1; // Usuario por defecto
            if (auth()->check()) {
                $usuario_id = auth()->id();
            }
            
            \Log::info('Creando venta', [
                'numero_venta' => $numero_venta,
                'usuario_id' => $usuario_id,
                'productos_count' => count($validated['productos'])
            ]);
            
            $venta_id = DB::table('ventas')->insertGetId([
                'numero_venta' => $numero_venta,
                'cliente_id' => $validated['cliente_id'],
                'mesa_id' => $validated['mesa_id'],
                'usuario_id' => $usuario_id,
                'subtotal' => $validated['subtotal'],
                'impuesto' => $validated['iva'],
                'descuento' => 0,
                'total' => $validated['total'],
                'estado' => 'pagado',
                'tipo_pago' => $validated['metodo_pago'],
                'tipo_pedido' => $validated['mesa_id'] ? 'local' : 'llevar',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insertar detalles y actualizar stock
            foreach ($validated['productos'] as $producto) {
                // Verificar stock disponible antes de procesar
                $producto_actual = DB::table('productos')->where('id', $producto['id'])->first();
                if (!$producto_actual) {
                    throw new \Exception("Producto no encontrado: ID " . $producto['id']);
                }
                if ($producto_actual->stock < $producto['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto_actual->nombre}. Disponible: {$producto_actual->stock}, Solicitado: {$producto['cantidad']}");
                }
                
                DB::table('detalle_ventas')->insert([
                    'venta_id' => $venta_id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio'],
                    'subtotal' => $producto['cantidad'] * $producto['precio'],
                    'estado' => 'entregado',  // Cambiado de estado_cocina a estado
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Actualizar stock
                DB::table('productos')
                    ->where('id', $producto['id'])
                    ->decrement('stock', $producto['cantidad']);
            }

            // Si hay mesa, cambiar estado a ocupada
            if ($validated['mesa_id']) {
                DB::table('mesas')
                    ->where('id', $validated['mesa_id'])
                    ->update([
                        'estado' => 'ocupada',
                        'updated_at' => now()
                    ]);
            }

            DB::commit();
            
            \Log::info('Venta creada exitosamente', [
                'venta_id' => $venta_id,
                'numero_venta' => $numero_venta
            ]);
            
            // Si es una petición AJAX, retornar JSON
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venta procesada exitosamente',
                    'venta_id' => $venta_id,
                    'numero_venta' => $numero_venta
                ]);
            }
            
            return redirect()->route('ventas.index')->with('success', 'Venta realizada exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error al procesar venta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar la venta: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }
    
    /**
     * Completar venta y liberar mesa
     */
    public function completarVenta($id)
    {
        DB::beginTransaction();
        
        try {
            $venta = DB::table('ventas')->where('id', $id)->first();
            
            if (!$venta) {
                throw new \Exception('Venta no encontrada');
            }
            
            // Si la venta tiene mesa asociada, liberarla
            if ($venta->mesa_id) {
                DB::table('mesas')
                    ->where('id', $venta->mesa_id)
                    ->update([
                        'estado' => 'disponible',
                        'updated_at' => now()
                    ]);
                    
                \Log::info('Mesa liberada', ['mesa_id' => $venta->mesa_id, 'venta_id' => $id]);
            }
            
            // Actualizar estado de la venta si es necesario
            DB::table('ventas')
                ->where('id', $id)
                ->update([
                    'estado' => 'completado',
                    'updated_at' => now()
                ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta completada y mesa liberada'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al completar venta', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al completar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Guardar venta desde AJAX (simplificado)
     */
    public function storeAjax(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Generar número de boleta/factura
            $ultimo_numero = DB::table('ventas')
                ->where('numero_venta', 'like', 'B%')
                ->orderBy('id', 'desc')
                ->value('numero_venta');
                
            if ($ultimo_numero) {
                $ultimo = intval(substr($ultimo_numero, 1));
                $nuevo_numero = 'B' . str_pad($ultimo + 1, 8, '0', STR_PAD_LEFT);
            } else {
                $nuevo_numero = 'B00000001';
            }
            
            // Validar datos requeridos
            $subtotal = floatval($request->subtotal ?? 0);
            $descuento = floatval($request->descuento ?? 0); 
            $impuesto = floatval($request->impuesto ?? 0);
            $total = floatval($request->total ?? 0);
            
            // Si no vienen los totales, calcular desde items
            if ($total == 0 && $request->items) {
                $subtotal = 0;
                foreach ($request->items as $item) {
                    $precio = floatval($item['precio_venta'] ?? $item['precio'] ?? 0);
                    $cantidad = intval($item['quantity'] ?? $item['cantidad'] ?? 1);
                    $subtotal += $precio * $cantidad;
                }
                $descuento = $subtotal * 0.10; // 10% descuento
                $impuesto = ($subtotal - $descuento) * 0.11; // 11% IGV
                $total = $subtotal - $descuento + $impuesto;
            }
            
            // Crear venta con número de boleta
            $venta_id = DB::table('ventas')->insertGetId([
                'numero_venta' => $nuevo_numero,
                'cliente_id' => $request->cliente_id ?? null,
                'mesa_id' => $request->mesa_id ?? null,
                'usuario_id' => auth()->id() ?? 1,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => 'pagado',
                'tipo_pago' => $request->tipo_pago ?? 'efectivo',
                'tipo_pedido' => $request->tipo_orden ?? 'llevar',
                'observaciones' => $request->cliente ?? 'Cliente General',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insertar detalles de productos
            if ($request->items && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $producto_id = $item['id'] ?? null;
                    $cantidad = intval($item['quantity'] ?? $item['cantidad'] ?? 1);
                    $precio_unitario = floatval($item['precio_venta'] ?? $item['precio'] ?? 0);
                    
                    // Solo insertar si hay producto_id válido
                    if ($producto_id) {
                        // Verificar stock disponible
                        $producto = DB::table('productos')->where('id', $producto_id)->first();
                        if ($producto && $producto->stock >= $cantidad) {
                            // Insertar detalle
                            DB::table('detalle_ventas')->insert([
                                'venta_id' => $venta_id,
                                'producto_id' => $producto_id,
                                'cantidad' => $cantidad,
                                'precio_unitario' => $precio_unitario,
                                'subtotal' => $cantidad * $precio_unitario,
                                'estado' => 'entregado',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            // Actualizar stock
                            DB::table('productos')
                                ->where('id', $producto_id)
                                ->decrement('stock', $cantidad);
                            
                            // Registrar movimiento de inventario
                            DB::table('movimientos_inventario')->insert([
                                'producto_id' => $producto_id,
                                'tipo_movimiento' => 'salida',
                                'cantidad' => $cantidad,
                                'stock_anterior' => $producto->stock,
                                'stock_nuevo' => $producto->stock - $cantidad,
                                'costo_unitario' => $precio_unitario,
                                'costo_total' => $cantidad * $precio_unitario,
                                'motivo' => 'Venta',
                                'documento_referencia' => $nuevo_numero,
                                'usuario_id' => auth()->id() ?? 1,
                                'venta_id' => $venta_id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            
            // Preparar datos para la boleta
            $venta = DB::table('ventas')->where('id', $venta_id)->first();
            $detalles = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->where('detalle_ventas.venta_id', $venta_id)
                ->select('productos.nombre', 'detalle_ventas.*')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'venta_id' => $venta_id,
                'numero_venta' => $nuevo_numero,
                'boleta' => [
                    'numero' => $nuevo_numero,
                    'fecha' => now()->format('d/m/Y H:i'),
                    'cliente' => $request->cliente ?? 'Cliente General',
                    'mesa' => $request->mesa_id ? 'Mesa ' . $request->mesa_id : 'Para llevar',
                    'items' => $detalles,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'impuesto' => $impuesto,
                    'total' => $total
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error en venta AJAX: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener historial de ventas
     */
    public function historial(Request $request)
    {
        try {
            $ventas = DB::table('ventas')
                ->leftJoin('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                ->leftJoin('mesas', 'ventas.mesa_id', '=', 'mesas.id')
                ->select(
                    'ventas.*',
                    'clientes.nombre as cliente_nombre',
                    'mesas.numero as mesa_numero'
                )
                ->orderBy('ventas.created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Agregar detalles de items a cada venta con imágenes
            $ventas = $ventas->map(function ($venta) {
                $items = DB::table('detalle_ventas')
                    ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                    ->where('detalle_ventas.venta_id', $venta->id)
                    ->select(
                        'productos.nombre', 
                        'productos.imagen',
                        'detalle_ventas.cantidad', 
                        'detalle_ventas.precio_unitario',
                        'detalle_ventas.subtotal'
                    )
                    ->get();
                
                $venta->items = $items;
                $venta->cliente = $venta->cliente_nombre ?? $venta->observaciones ?? 'Cliente General';
                $venta->mesa_id = $venta->mesa_numero ?? 'N/A';
                $venta->fecha = $venta->created_at;
                
                return $venta;
            });
            
            return response()->json([
                'success' => true,
                'ventas' => $ventas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial: ' . $e->getMessage(),
                'ventas' => []
            ], 500);
        }
    }
}