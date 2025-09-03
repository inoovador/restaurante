<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

// Las rutas de autenticación están en routes/auth.php

// API Routes para el carrito (sin autenticación para pruebas)
Route::prefix('api')->group(function () {
    Route::post('/ventas', [\App\Http\Controllers\VentaController::class, 'storeAjax']);
    Route::get('/ventas/historial', [\App\Http\Controllers\VentaController::class, 'historial']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    
    // Categorías
    Route::get('/categorias', [\App\Http\Controllers\CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [\App\Http\Controllers\CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{id}', [\App\Http\Controllers\CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{id}', [\App\Http\Controllers\CategoriaController::class, 'destroy'])->name('categorias.destroy');
    
    // Productos
    Route::get('/productos', [\App\Http\Controllers\ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [\App\Http\Controllers\ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [\App\Http\Controllers\ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [\App\Http\Controllers\ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::post('/productos/{id}/imagen', [\App\Http\Controllers\ProductoController::class, 'updateImage'])->name('productos.updateImage');
    
    // Mesas
    Route::get('/mesas', [\App\Http\Controllers\MesaController::class, 'index'])->name('mesas.index');
    Route::put('/mesas/{id}/estado', [\App\Http\Controllers\MesaController::class, 'updateEstado'])->name('mesas.updateEstado');
    
    // Ventas (POS)
    Route::get('/ventas', [\App\Http\Controllers\VentaController::class, 'index'])->name('ventas.index');
    Route::post('/ventas', [\App\Http\Controllers\VentaController::class, 'store'])->name('ventas.store');
    Route::post('/ventas/{id}/completar', [\App\Http\Controllers\VentaController::class, 'completarVenta'])->name('ventas.completar');
    
    // Usuarios y Roles
    Route::get('/usuarios', [\App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/roles', [\App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
    
    // Clientes
    Route::get('/clientes', [\App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
    
    // Pedidos
    Route::get('/pedidos', [\App\Http\Controllers\PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{id}', [\App\Http\Controllers\PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{id}/print', [\App\Http\Controllers\PedidoController::class, 'print'])->name('pedidos.print');
    Route::patch('/pedidos/{id}/estado', [\App\Http\Controllers\PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus');
    
    // Caja
    Route::get('/caja', [\App\Http\Controllers\CajaController::class, 'index'])->name('caja.index');
    
    // Inventario
    Route::get('/inventario', [\App\Http\Controllers\InventarioController::class, 'index'])->name('inventario.index');
    
    // Módulos adicionales
    Route::get('/compras', [\App\Http\Controllers\ComprasController::class, 'index'])->name('compras.index');
    Route::get('/barra', [\App\Http\Controllers\BarraController::class, 'index'])->name('barra.index');
    Route::get('/cocina', [\App\Http\Controllers\CocinaController::class, 'index'])->name('cocina.index');
    
    // Configuración
    Route::get('/configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');
    
    // Reportes
    Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/export', [\App\Http\Controllers\ReporteController::class, 'export'])->name('reportes.export');
    
    // API Carrito
    Route::prefix('api/cart')->group(function () {
        Route::post('/add', [\App\Http\Controllers\CartController::class, 'addProduct'])->name('api.cart.add');
        Route::get('/', [\App\Http\Controllers\CartController::class, 'getCart'])->name('api.cart.get');
        Route::patch('/update', [\App\Http\Controllers\CartController::class, 'updateQuantity'])->name('api.cart.update');
        Route::delete('/clear', [\App\Http\Controllers\CartController::class, 'clearCart'])->name('api.cart.clear');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
