<?php
// Script para verificar el sistema completo
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DEL SISTEMA ===\n\n";

try {
    // Verificar conexión a la base de datos
    DB::connection()->getPdo();
    echo "✅ Conexión a base de datos: OK\n";
    
    // Verificar tablas principales
    $tables = ['users', 'categorias', 'productos', 'mesas', 'clientes', 'ventas'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "✅ Tabla $table: $count registros\n";
        } catch (Exception $e) {
            echo "❌ Tabla $table: ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== DATOS EN EL SISTEMA ===\n";
    
    // Verificar categorías
    $categorias = DB::table('categorias')->get();
    echo "\n🏷️ CATEGORÍAS:\n";
    foreach ($categorias as $categoria) {
        echo "   - {$categoria->nombre} ({$categoria->tipo})\n";
    }
    
    // Verificar productos
    $productos = DB::table('productos')
        ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->select('productos.nombre', 'productos.precio_venta', 'categorias.nombre as categoria')
        ->take(5)
        ->get();
    echo "\n🍕 PRODUCTOS (primeros 5):\n";
    foreach ($productos as $producto) {
        echo "   - {$producto->nombre} (\${$producto->precio_venta}) - {$producto->categoria}\n";
    }
    
    // Verificar mesas
    $mesas = DB::table('mesas')->get();
    echo "\n🪑 MESAS:\n";
    foreach ($mesas as $mesa) {
        echo "   - Mesa {$mesa->numero} ({$mesa->zona}) - {$mesa->estado}\n";
    }
    
    // Verificar clientes
    $clientes = DB::table('clientes')->count();
    echo "\n👥 CLIENTES: $clientes registrados\n";
    
    echo "\n=== VERIFICACIÓN DE ARCHIVOS ===\n";
    
    // Verificar vistas principales
    $views = [
        'resources/views/ventas/content.blade.php' => 'Vista POS',
        'resources/views/productos/content.blade.php' => 'Vista Productos',
        'resources/views/categorias/content.blade.php' => 'Vista Categorías',
        'resources/js/pages/HybridPage.tsx' => 'Componente Híbrido React'
    ];
    
    foreach ($views as $file => $name) {
        if (file_exists($file)) {
            echo "✅ $name: OK\n";
        } else {
            echo "❌ $name: FALTA\n";
        }
    }
    
    echo "\n=== RUTAS DISPONIBLES ===\n";
    echo "📍 Login: http://localhost:8000/login\n";
    echo "📍 Dashboard: http://localhost:8000/dashboard\n";
    echo "📍 POS: http://localhost:8000/ventas\n";
    echo "📍 Productos: http://localhost:8000/productos\n";
    echo "📍 Categorías: http://localhost:8000/categorias\n";
    
    echo "\n=== CREDENCIALES ===\n";
    $user = DB::table('users')->first();
    if ($user) {
        echo "👤 Usuario: {$user->email}\n";
        echo "🔑 Password: password (por defecto)\n";
    }
    
    echo "\n✅ SISTEMA LISTO PARA USAR!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo "Ejecuta setup_db.php para configurar la base de datos\n";
}
?>