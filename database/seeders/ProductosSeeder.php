<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero verificar si existen categorías, si no, crearlas
        $categorias = DB::table('categorias')->get();
        
        if ($categorias->isEmpty()) {
            // Crear categorías básicas
            DB::table('categorias')->insert([
                ['id' => 1, 'nombre' => 'Entradas', 'descripcion' => 'Platos de entrada', 'color' => '#10B981', 'icono' => 'fa-utensils', 'tipo' => 'comida', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'nombre' => 'Platos Principales', 'descripcion' => 'Platos principales', 'color' => '#EF4444', 'icono' => 'fa-drumstick-bite', 'tipo' => 'comida', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'nombre' => 'Postres', 'descripcion' => 'Postres y dulces', 'color' => '#F59E0B', 'icono' => 'fa-ice-cream', 'tipo' => 'comida', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'color' => '#3B82F6', 'icono' => 'fa-coffee', 'tipo' => 'bebida', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 5, 'nombre' => 'Sopas', 'descripcion' => 'Sopas y caldos', 'color' => '#8B5CF6', 'icono' => 'fa-bowl-hot', 'tipo' => 'comida', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        
        // Verificar si ya existen productos
        $existingProducts = DB::table('productos')->count();
        
        if ($existingProducts == 0) {
            // Insertar productos de ejemplo
            $productos = [
                // Entradas
                ['codigo' => 'ENT001', 'nombre' => 'Ceviche de Pescado', 'descripcion' => 'Pescado fresco marinado en limón con cebolla y ají', 'categoria_id' => 1, 'precio_venta' => 35.00, 'precio_compra' => 20.00, 'stock' => 50, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'ENT002', 'nombre' => 'Ensalada César', 'descripcion' => 'Lechuga, crutones, queso parmesano y aderezo césar', 'categoria_id' => 1, 'precio_venta' => 25.00, 'precio_compra' => 15.00, 'stock' => 30, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'ENT003', 'nombre' => 'Anticuchos', 'descripcion' => 'Brochetas de corazón de res con papas y choclo', 'categoria_id' => 1, 'precio_venta' => 28.00, 'precio_compra' => 18.00, 'stock' => 40, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'ENT004', 'nombre' => 'Papa a la Huancaína', 'descripcion' => 'Papas con crema de ají amarillo', 'categoria_id' => 1, 'precio_venta' => 22.00, 'precio_compra' => 12.00, 'stock' => 35, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                
                // Platos Principales
                ['codigo' => 'PRI001', 'nombre' => 'Lomo Saltado', 'descripcion' => 'Lomo de res salteado con cebolla, tomate y papas fritas', 'categoria_id' => 2, 'precio_venta' => 42.00, 'precio_compra' => 25.00, 'stock' => 30, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'PRI002', 'nombre' => 'Ají de Gallina', 'descripcion' => 'Pollo deshilachado en crema de ají amarillo', 'categoria_id' => 2, 'precio_venta' => 38.00, 'precio_compra' => 22.00, 'stock' => 25, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'PRI003', 'nombre' => 'Arroz con Mariscos', 'descripcion' => 'Arroz con variedad de mariscos frescos', 'categoria_id' => 2, 'precio_venta' => 48.00, 'precio_compra' => 30.00, 'stock' => 20, 'stock_minimo' => 8, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'PRI004', 'nombre' => 'Filete de Pescado', 'descripcion' => 'Filete de pescado a la plancha con guarnición', 'categoria_id' => 2, 'precio_venta' => 45.00, 'precio_compra' => 28.00, 'stock' => 25, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'PRI005', 'nombre' => 'Pollo a la Brasa', 'descripcion' => '1/4 de pollo con papas fritas y ensalada', 'categoria_id' => 2, 'precio_venta' => 32.00, 'precio_compra' => 18.00, 'stock' => 40, 'stock_minimo' => 15, 'unidad' => 'plato', 'activo' => true],
                
                // Postres
                ['codigo' => 'POS001', 'nombre' => 'Suspiro Limeño', 'descripcion' => 'Dulce de leche con merengue', 'categoria_id' => 3, 'precio_venta' => 18.00, 'precio_compra' => 10.00, 'stock' => 20, 'stock_minimo' => 8, 'unidad' => 'porción', 'activo' => true],
                ['codigo' => 'POS002', 'nombre' => 'Tres Leches', 'descripcion' => 'Bizcocho bañado en tres tipos de leche', 'categoria_id' => 3, 'precio_venta' => 16.00, 'precio_compra' => 9.00, 'stock' => 25, 'stock_minimo' => 10, 'unidad' => 'porción', 'activo' => true],
                ['codigo' => 'POS003', 'nombre' => 'Picarones', 'descripcion' => 'Buñuelos con miel de chancaca', 'categoria_id' => 3, 'precio_venta' => 15.00, 'precio_compra' => 8.00, 'stock' => 30, 'stock_minimo' => 10, 'unidad' => 'porción', 'activo' => true],
                ['codigo' => 'POS004', 'nombre' => 'Flan de Vainilla', 'descripcion' => 'Flan casero con caramelo', 'categoria_id' => 3, 'precio_venta' => 12.00, 'precio_compra' => 6.00, 'stock' => 35, 'stock_minimo' => 15, 'unidad' => 'porción', 'activo' => true],
                
                // Bebidas
                ['codigo' => 'BEB001', 'nombre' => 'Pisco Sour', 'descripcion' => 'Coctel tradicional peruano', 'categoria_id' => 4, 'precio_venta' => 25.00, 'precio_compra' => 12.00, 'stock' => 50, 'stock_minimo' => 20, 'unidad' => 'vaso', 'activo' => true],
                ['codigo' => 'BEB002', 'nombre' => 'Chicha Morada', 'descripcion' => 'Bebida de maíz morado', 'categoria_id' => 4, 'precio_venta' => 12.00, 'precio_compra' => 5.00, 'stock' => 60, 'stock_minimo' => 25, 'unidad' => 'jarra', 'activo' => true],
                ['codigo' => 'BEB003', 'nombre' => 'Limonada Frozen', 'descripcion' => 'Limonada frozen natural', 'categoria_id' => 4, 'precio_venta' => 10.00, 'precio_compra' => 4.00, 'stock' => 70, 'stock_minimo' => 30, 'unidad' => 'vaso', 'activo' => true],
                ['codigo' => 'BEB004', 'nombre' => 'Café Americano', 'descripcion' => 'Café negro americano', 'categoria_id' => 4, 'precio_venta' => 8.00, 'precio_compra' => 3.00, 'stock' => 100, 'stock_minimo' => 40, 'unidad' => 'taza', 'activo' => true],
                ['codigo' => 'BEB005', 'nombre' => 'Jugo de Naranja', 'descripcion' => 'Jugo natural de naranja', 'categoria_id' => 4, 'precio_venta' => 10.00, 'precio_compra' => 4.00, 'stock' => 80, 'stock_minimo' => 30, 'unidad' => 'vaso', 'activo' => true],
                
                // Sopas
                ['codigo' => 'SOP001', 'nombre' => 'Sopa a la Criolla', 'descripcion' => 'Sopa tradicional con fideos y carne', 'categoria_id' => 5, 'precio_venta' => 20.00, 'precio_compra' => 12.00, 'stock' => 30, 'stock_minimo' => 10, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'SOP002', 'nombre' => 'Chupe de Camarones', 'descripcion' => 'Sopa cremosa con camarones', 'categoria_id' => 5, 'precio_venta' => 35.00, 'precio_compra' => 20.00, 'stock' => 20, 'stock_minimo' => 8, 'unidad' => 'plato', 'activo' => true],
                ['codigo' => 'SOP003', 'nombre' => 'Aguadito de Pollo', 'descripcion' => 'Sopa de pollo con arroz y verduras', 'categoria_id' => 5, 'precio_venta' => 18.00, 'precio_compra' => 10.00, 'stock' => 35, 'stock_minimo' => 12, 'unidad' => 'plato', 'activo' => true],
            ];
            
            foreach ($productos as $producto) {
                $producto['imagen'] = null; // Por ahora sin imágenes
                $producto['created_at'] = now();
                $producto['updated_at'] = now();
                DB::table('productos')->insert($producto);
            }
            
            $this->command->info('Se insertaron ' . count($productos) . ' productos de ejemplo.');
        } else {
            $this->command->info('Ya existen productos en la base de datos.');
        }
    }
}