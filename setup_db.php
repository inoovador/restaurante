<?php
// Script para configurar la base de datos
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CONFIGURANDO BASE DE DATOS ===\n\n";

try {
    echo "1. Ejecutando migraciones...\n";
    Artisan::call('migrate:fresh', ['--force' => true]);
    echo "✅ Migraciones completadas\n\n";
    
    echo "2. Ejecutando seeders...\n";
    Artisan::call('db:seed', ['--force' => true]);
    echo "✅ Datos de prueba insertados\n\n";
    
    echo "=== CONFIGURACIÓN COMPLETA ===\n";
    echo "Usuario de prueba creado:\n";
    echo "Email: admin@restaurant.com\n";
    echo "Password: password\n\n";
    
    echo "Datos insertados:\n";
    echo "- 4 categorías de productos\n";
    echo "- 10 productos de ejemplo\n";
    echo "- 6 mesas\n";
    echo "- 4 clientes\n\n";
    
    echo "¡Ya puedes usar el sistema!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Verifica que la base de datos esté configurada correctamente en .env\n";
}
?>