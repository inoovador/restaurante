<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function addProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'cantidad' => 'integer|min:1'
        ]);

        $productId = $request->input('product_id');
        $cantidad = $request->input('cantidad', 1);

        // Obtener detalles completos del producto incluyendo imagen
        $producto = DB::table('productos')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('productos.id', $productId)
            ->where('productos.activo', true)
            ->select(
                'productos.id',
                'productos.codigo',
                'productos.nombre',
                'productos.descripcion',
                'productos.precio_venta',
                'productos.stock',
                'productos.imagen',
                'categorias.nombre as categoria_nombre',
                'categorias.color as categoria_color'
            )
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar stock disponible
        if ($producto->stock < $cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . $producto->stock
            ], 400);
        }

        // Procesar URL de imagen
        $imagenUrl = null;
        if ($producto->imagen) {
            if (file_exists(public_path($producto->imagen))) {
                $imagenUrl = '/' . $producto->imagen;
            } elseif (file_exists(public_path('uploads/productos/' . basename($producto->imagen)))) {
                $imagenUrl = '/uploads/productos/' . basename($producto->imagen);
            }
        }

        // Obtener carrito actual de la sesión
        $carrito = Session::get('carrito', []);
        
        // Verificar si el producto ya está en el carrito
        $encontrado = false;
        foreach ($carrito as &$item) {
            if ($item['id'] == $productId) {
                $nuevaCantidad = $item['cantidad'] + $cantidad;
                
                // Verificar stock para la nueva cantidad
                if ($producto->stock < $nuevaCantidad) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente. Ya tienes ' . $item['cantidad'] . ' en el carrito. Disponible: ' . $producto->stock
                    ], 400);
                }
                
                $item['cantidad'] = $nuevaCantidad;
                $item['subtotal'] = $item['cantidad'] * $item['precio_venta'];
                $encontrado = true;
                break;
            }
        }

        // Si no está en el carrito, agregarlo
        if (!$encontrado) {
            $carrito[] = [
                'id' => $producto->id,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio_venta' => $producto->precio_venta,
                'cantidad' => $cantidad,
                'subtotal' => $producto->precio_venta * $cantidad,
                'categoria_nombre' => $producto->categoria_nombre,
                'categoria_color' => $producto->categoria_color,
                'imagen_url' => $imagenUrl,
                'stock_disponible' => $producto->stock
            ];
        }

        // Guardar carrito en sesión
        Session::put('carrito', $carrito);

        // Calcular totales
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $subtotal = array_sum(array_column($carrito, 'subtotal'));
        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'cantidad' => $cantidad,
                'imagen_url' => $imagenUrl
            ],
            'carrito' => $carrito,
            'totales' => [
                'items' => $totalItems,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total
            ]
        ]);
    }

    public function getCart()
    {
        $carrito = Session::get('carrito', []);
        
        // Calcular totales
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $subtotal = array_sum(array_column($carrito, 'subtotal'));
        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        return response()->json([
            'success' => true,
            'carrito' => $carrito,
            'totales' => [
                'items' => $totalItems,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total
            ]
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'cantidad' => 'required|integer|min:0'
        ]);

        $productId = $request->input('product_id');
        $nuevaCantidad = $request->input('cantidad');
        
        $carrito = Session::get('carrito', []);

        foreach ($carrito as $key => &$item) {
            if ($item['id'] == $productId) {
                if ($nuevaCantidad == 0) {
                    // Eliminar item del carrito
                    unset($carrito[$key]);
                } else {
                    // Verificar stock disponible
                    if ($item['stock_disponible'] < $nuevaCantidad) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stock insuficiente. Disponible: ' . $item['stock_disponible']
                        ], 400);
                    }
                    
                    $item['cantidad'] = $nuevaCantidad;
                    $item['subtotal'] = $item['cantidad'] * $item['precio_venta'];
                }
                break;
            }
        }

        // Reindexar array
        $carrito = array_values($carrito);
        Session::put('carrito', $carrito);

        return $this->getCart();
    }

    public function clearCart()
    {
        Session::forget('carrito');
        
        return response()->json([
            'success' => true,
            'message' => 'Carrito limpiado',
            'carrito' => [],
            'totales' => [
                'items' => 0,
                'subtotal' => 0,
                'igv' => 0,
                'total' => 0
            ]
        ]);
    }
}