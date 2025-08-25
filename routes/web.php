<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Módulos del Restaurante
    Route::get('/categorias', [\App\Http\Controllers\CategoriaController::class, 'index'])->name('categorias.index');
    
    // Productos
    Route::get('/productos', [\App\Http\Controllers\ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [\App\Http\Controllers\ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [\App\Http\Controllers\ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [\App\Http\Controllers\ProductoController::class, 'destroy'])->name('productos.destroy');
    
    // Mesas
    Route::get('/mesas', [\App\Http\Controllers\MesaController::class, 'index'])->name('mesas.index');
    Route::put('/mesas/{id}/estado', [\App\Http\Controllers\MesaController::class, 'updateEstado'])->name('mesas.updateEstado');
    
    // Ventas (POS)
    Route::get('/ventas', [\App\Http\Controllers\VentaController::class, 'index'])->name('ventas.index');
    Route::post('/ventas', [\App\Http\Controllers\VentaController::class, 'store'])->name('ventas.store');
    
    // Usuarios y Roles
    Route::get('/usuarios', [\App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/roles', [\App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
    
    // Clientes
    Route::get('/clientes', [\App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
    
    // Caja
    Route::get('/caja', [\App\Http\Controllers\CajaController::class, 'index'])->name('caja.index');
    
    // Inventario
    Route::get('/inventario', [\App\Http\Controllers\InventarioController::class, 'index'])->name('inventario.index');
    
    // Módulos adicionales
    Route::get('/compras', [\App\Http\Controllers\ComprasController::class, 'index'])->name('compras.index');
    Route::get('/barra', [\App\Http\Controllers\BarraController::class, 'index'])->name('barra.index');
    Route::get('/cocina', [\App\Http\Controllers\CocinaController::class, 'index'])->name('cocina.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
