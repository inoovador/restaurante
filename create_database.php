<?php
// Script para crear la base de datos restaurant
echo "=== CREANDO BASE DE DATOS ===\n\n";

$config = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'username' => 'root',
    'password' => '',
    'database' => 'restaurant'
];

echo "Intentando conectar a MySQL...\n";

try {
    // Intentar conectar sin especificar la base de datos
    $pdo = new PDO("mysql:host={$config['host']};port={$config['port']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión a MySQL exitosa\n";
    
    // Crear la base de datos si no existe
    $sql = "CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "✅ Base de datos '{$config['database']}' creada o ya existe\n";
    
    // Verificar que la base de datos existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['database']}'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Base de datos confirmada\n";
    } else {
        echo "❌ No se pudo crear la base de datos\n";
    }
    
    // Probar conexión a la base de datos específica
    $pdo_db = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}", $config['username'], $config['password']);
    $pdo_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión a la base de datos restaurant exitosa\n\n";
    
    echo "=== ESTADO ACTUAL ===\n";
    echo "Host: {$config['host']}\n";
    echo "Puerto: {$config['port']}\n";
    echo "Usuario: {$config['username']}\n";
    echo "Base de datos: {$config['database']}\n";
    echo "¡Listo para ejecutar migraciones!\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "Posibles soluciones:\n";
    echo "1. Asegúrate de que XAMPP esté ejecutándose\n";
    echo "2. Inicia MySQL desde el panel de control de XAMPP\n";
    echo "3. Verifica que el puerto 3306 esté disponible\n";
    echo "4. Comprueba las credenciales en el archivo .env\n";
    
    exit(1);
}
?>