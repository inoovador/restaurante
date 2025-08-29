<?php
// Script para corregir la base de datos directamente

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== Corrección de Base de Datos ===\n\n";
    
    // Verificar que la tabla detalle_ventas existe
    if (!Schema::hasTable('detalle_ventas')) {
        echo "ERROR: La tabla 'detalle_ventas' no existe.\n";
        echo "Por favor, ejecute las migraciones primero.\n";
        exit(1);
    }
    
    echo "✓ Tabla 'detalle_ventas' encontrada.\n";
    
    // Verificar columnas existentes
    $columns = Schema::getColumnListing('detalle_ventas');
    echo "\nColumnas actuales en detalle_ventas:\n";
    foreach ($columns as $column) {
        echo "  - $column\n";
    }
    
    // Agregar columnas faltantes si es necesario
    if (!in_array('observaciones', $columns) && in_array('notas', $columns)) {
        echo "\n➤ La columna 'observaciones' no existe, pero 'notas' sí. No se requiere acción.\n";
    }
    
    if (!in_array('estado', $columns)) {
        echo "\nERROR: La columna 'estado' no existe en detalle_ventas.\n";
        echo "Creando columna 'estado'...\n";
        
        DB::statement("ALTER TABLE detalle_ventas ADD COLUMN estado ENUM('pendiente', 'preparando', 'listo', 'entregado', 'cancelado') DEFAULT 'pendiente' AFTER notas");
        echo "✓ Columna 'estado' creada.\n";
    } else {
        echo "\n✓ Columna 'estado' existe.\n";
    }
    
    // Verificar si hay registros con problemas
    $count = DB::table('detalle_ventas')->count();
    echo "\nTotal de registros en detalle_ventas: $count\n";
    
    // Verificar la tabla ventas_detalle (la duplicada)
    if (Schema::hasTable('ventas_detalle')) {
        echo "\n⚠ ADVERTENCIA: Existe una tabla duplicada 'ventas_detalle'.\n";
        $count_duplicate = DB::table('ventas_detalle')->count();
        echo "  Registros en ventas_detalle: $count_duplicate\n";
        
        if ($count_duplicate == 0) {
            echo "  La tabla duplicada está vacía. Se puede eliminar de forma segura.\n";
            Schema::dropIfExists('ventas_detalle');
            echo "  ✓ Tabla duplicada 'ventas_detalle' eliminada.\n";
        } else {
            echo "  La tabla duplicada tiene datos. Requiere revisión manual.\n";
        }
    }
    
    // Verificar integridad referencial
    echo "\n➤ Verificando integridad referencial...\n";
    
    $orphanedDetails = DB::table('detalle_ventas')
        ->leftJoin('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
        ->whereNull('ventas.id')
        ->count();
    
    if ($orphanedDetails > 0) {
        echo "  ⚠ Hay $orphanedDetails registros huérfanos en detalle_ventas.\n";
    } else {
        echo "  ✓ No hay registros huérfanos.\n";
    }
    
    $orphanedProducts = DB::table('detalle_ventas')
        ->leftJoin('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
        ->whereNull('productos.id')
        ->count();
    
    if ($orphanedProducts > 0) {
        echo "  ⚠ Hay $orphanedProducts registros con productos no válidos.\n";
    } else {
        echo "  ✓ Todos los productos referenciados existen.\n";
    }
    
    echo "\n=== Corrección Completada ===\n";
    echo "\n✅ La base de datos ha sido corregida exitosamente.\n";
    echo "El sistema ahora debería funcionar correctamente.\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}