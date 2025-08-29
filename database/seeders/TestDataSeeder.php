<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar datos existentes (compatible con SQLite)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } else {
            DB::statement('PRAGMA foreign_keys=OFF;');
        }
        
        DB::table('detalle_ventas')->delete();
        DB::table('ventas')->delete();
        DB::table('productos')->delete();
        DB::table('categorias')->delete();
        DB::table('mesas')->delete();
        DB::table('clientes')->delete();
        
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            DB::statement('PRAGMA foreign_keys=ON;');
        }

        // Insertar categorías
        $categorias = [
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas y refrescos', 'tipo' => 'bebida', 'area' => 'barra', 'color' => '#3B82F6', 'activo' => true],
            ['nombre' => 'Entradas', 'descripcion' => 'Aperitivos y entradas', 'tipo' => 'comida', 'area' => 'cocina', 'color' => '#EF4444', 'activo' => true],
            ['nombre' => 'Platos Principales', 'descripcion' => 'Platos fuertes', 'tipo' => 'comida', 'area' => 'cocina', 'color' => '#10B981', 'activo' => true],
            ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres', 'tipo' => 'comida', 'area' => 'cocina', 'color' => '#F59E0B', 'activo' => true],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias')->insert([
                ...$categoria,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Obtener IDs de categorías
        $bebidas_id = DB::table('categorias')->where('nombre', 'Bebidas')->first()->id;
        $entradas_id = DB::table('categorias')->where('nombre', 'Entradas')->first()->id;
        $principales_id = DB::table('categorias')->where('nombre', 'Platos Principales')->first()->id;
        $postres_id = DB::table('categorias')->where('nombre', 'Postres')->first()->id;

        // Insertar productos
        $productos = [
            // Bebidas
            ['codigo' => 'BEB001', 'nombre' => 'Coca Cola', 'descripcion' => 'Refresco de cola 350ml', 'categoria_id' => $bebidas_id, 'precio_venta' => 2.50, 'precio_compra' => 1.50, 'stock' => 100, 'stock_minimo' => 20],
            ['codigo' => 'BEB002', 'nombre' => 'Agua Natural', 'descripcion' => 'Agua purificada 500ml', 'categoria_id' => $bebidas_id, 'precio_venta' => 1.00, 'precio_compra' => 0.60, 'stock' => 150, 'stock_minimo' => 30],
            ['codigo' => 'BEB003', 'nombre' => 'Cerveza Corona', 'descripcion' => 'Cerveza 355ml', 'categoria_id' => $bebidas_id, 'precio_venta' => 3.50, 'precio_compra' => 2.20, 'stock' => 80, 'stock_minimo' => 15],
            
            // Entradas
            ['codigo' => 'ENT001', 'nombre' => 'Nachos con Queso', 'descripcion' => 'Nachos crujientes con salsa de queso', 'categoria_id' => $entradas_id, 'precio_venta' => 8.50, 'precio_compra' => 4.50, 'stock' => 50, 'stock_minimo' => 10],
            ['codigo' => 'ENT002', 'nombre' => 'Alitas BBQ', 'descripcion' => '8 alitas de pollo en salsa BBQ', 'categoria_id' => $entradas_id, 'precio_venta' => 12.00, 'precio_compra' => 7.00, 'stock' => 30, 'stock_minimo' => 8],
            
            // Platos principales
            ['codigo' => 'PLA001', 'nombre' => 'Hamburguesa Clásica', 'descripcion' => 'Hamburguesa de carne con papas fritas', 'categoria_id' => $principales_id, 'precio_venta' => 15.50, 'precio_compra' => 9.50, 'stock' => 40, 'stock_minimo' => 8],
            ['codigo' => 'PLA002', 'nombre' => 'Pizza Margarita', 'descripcion' => 'Pizza con tomate, mozzarella y albahaca', 'categoria_id' => $principales_id, 'precio_venta' => 18.00, 'precio_compra' => 10.00, 'stock' => 25, 'stock_minimo' => 5],
            ['codigo' => 'PLA003', 'nombre' => 'Tacos de Pollo', 'descripcion' => '3 tacos de pollo con guarniciones', 'categoria_id' => $principales_id, 'precio_venta' => 13.50, 'precio_compra' => 8.00, 'stock' => 35, 'stock_minimo' => 10],
            
            // Postres
            ['codigo' => 'POS001', 'nombre' => 'Cheesecake', 'descripcion' => 'Pastel de queso con fresas', 'categoria_id' => $postres_id, 'precio_venta' => 6.50, 'precio_compra' => 3.50, 'stock' => 20, 'stock_minimo' => 5],
            ['codigo' => 'POS002', 'nombre' => 'Helado de Vainilla', 'descripcion' => '3 bolas de helado con topping', 'categoria_id' => $postres_id, 'precio_venta' => 4.50, 'precio_compra' => 2.50, 'stock' => 50, 'stock_minimo' => 10],
        ];

        foreach ($productos as $producto) {
            DB::table('productos')->insert([
                ...$producto,
                'unidad' => 'unidad',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insertar mesas
        $mesas = [
            ['numero' => '1', 'capacidad' => 2, 'zona' => 'terraza', 'estado' => 'disponible', 'activo' => true],
            ['numero' => '2', 'capacidad' => 4, 'zona' => 'salon_principal', 'estado' => 'disponible', 'activo' => true],
            ['numero' => '3', 'capacidad' => 4, 'zona' => 'salon_principal', 'estado' => 'ocupada', 'activo' => true],
            ['numero' => '4', 'capacidad' => 6, 'zona' => 'vip', 'estado' => 'disponible', 'activo' => true],
            ['numero' => '5', 'capacidad' => 2, 'zona' => 'terraza', 'estado' => 'disponible', 'activo' => true],
            ['numero' => '6', 'capacidad' => 8, 'zona' => 'vip', 'estado' => 'reservada', 'activo' => true],
        ];

        foreach ($mesas as $mesa) {
            DB::table('mesas')->insert([
                ...$mesa,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insertar clientes
        $clientes = [
            ['nombre' => 'Juan Pérez', 'telefono' => '555-0001', 'email' => 'juan@email.com', 'direccion' => 'Calle 123'],
            ['nombre' => 'María González', 'telefono' => '555-0002', 'email' => 'maria@email.com', 'direccion' => 'Av. Principal 456'],
            ['nombre' => 'Carlos López', 'telefono' => '555-0003', 'email' => 'carlos@email.com', 'direccion' => 'Boulevard Norte 789'],
            ['nombre' => 'Ana Martínez', 'telefono' => '555-0004', 'email' => 'ana@email.com', 'direccion' => 'Centro 321'],
        ];

        foreach ($clientes as $cliente) {
            DB::table('clientes')->insert([
                ...$cliente,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}