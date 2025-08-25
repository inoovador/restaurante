<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Gerente', 'descripcion' => 'Gestión del restaurante'],
            ['nombre' => 'Cajero', 'descripcion' => 'Manejo de caja y ventas'],
            ['nombre' => 'Mesero', 'descripcion' => 'Atención a clientes'],
            ['nombre' => 'Cocinero', 'descripcion' => 'Preparación de alimentos'],
            ['nombre' => 'Bartender', 'descripcion' => 'Preparación de bebidas'],
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->insert($role + ['created_at' => now(), 'updated_at' => now()]);
        }

        // Categorías con colores del tema
        $categorias = [
            ['nombre' => 'Entradas', 'descripcion' => 'Platos de entrada', 'tipo' => 'comida', 'area' => 'cocina', 'color' => '#E32636'],
            ['nombre' => 'Platos Principales', 'descripcion' => 'Platos fuertes', 'tipo' => 'comida', 'area' => 'cocina', 'color' => '#4d82bc'],
            ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres', 'tipo' => 'postre', 'area' => 'cocina', 'color' => '#E32636'],
            ['nombre' => 'Bebidas Calientes', 'descripcion' => 'Café, té, chocolate', 'tipo' => 'bebida', 'area' => 'barra', 'color' => '#4d82bc'],
            ['nombre' => 'Bebidas Frías', 'descripcion' => 'Jugos, gaseosas, agua', 'tipo' => 'bebida', 'area' => 'barra', 'color' => '#E32636'],
            ['nombre' => 'Cocteles', 'descripcion' => 'Bebidas con alcohol', 'tipo' => 'bebida', 'area' => 'barra', 'color' => '#4d82bc'],
            ['nombre' => 'Vinos', 'descripcion' => 'Vinos nacionales e importados', 'tipo' => 'bebida', 'area' => 'barra', 'color' => '#E32636'],
            ['nombre' => 'Cervezas', 'descripcion' => 'Cervezas artesanales y comerciales', 'tipo' => 'bebida', 'area' => 'barra', 'color' => '#4d82bc'],
        ];
        
        foreach ($categorias as $categoria) {
            DB::table('categorias')->insert($categoria + ['activo' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Mesas
        $mesas = [
            ['numero' => '1', 'capacidad' => 4, 'zona' => 'salon_principal'],
            ['numero' => '2', 'capacidad' => 4, 'zona' => 'salon_principal'],
            ['numero' => '3', 'capacidad' => 2, 'zona' => 'salon_principal'],
            ['numero' => '4', 'capacidad' => 6, 'zona' => 'salon_principal'],
            ['numero' => '5', 'capacidad' => 4, 'zona' => 'salon_principal'],
            ['numero' => 'T1', 'capacidad' => 4, 'zona' => 'terraza'],
            ['numero' => 'T2', 'capacidad' => 4, 'zona' => 'terraza'],
            ['numero' => 'B1', 'capacidad' => 1, 'zona' => 'barra'],
            ['numero' => 'B2', 'capacidad' => 1, 'zona' => 'barra'],
            ['numero' => 'VIP1', 'capacidad' => 10, 'zona' => 'vip'],
        ];
        
        foreach ($mesas as $mesa) {
            DB::table('mesas')->insert($mesa + [
                'estado' => 'disponible',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Clientes de ejemplo
        $clientes = [
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'telefono' => '555-0001', 'email' => 'juan@email.com'],
            ['nombre' => 'María', 'apellido' => 'González', 'telefono' => '555-0002', 'email' => 'maria@email.com'],
            ['nombre' => 'Carlos', 'apellido' => 'López', 'telefono' => '555-0003', 'email' => 'carlos@email.com'],
        ];
        
        foreach ($clientes as $cliente) {
            DB::table('clientes')->insert($cliente + [
                'visitas' => rand(1, 20),
                'total_gastado' => rand(50, 500),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Proveedores
        $proveedores = [
            ['nombre' => 'Distribuidora Alimentos S.A.', 'ruc' => '20100000001', 'telefono' => '555-1001'],
            ['nombre' => 'Carnes Premium', 'ruc' => '20100000002', 'telefono' => '555-1002'],
            ['nombre' => 'Bebidas del Sur', 'ruc' => '20100000003', 'telefono' => '555-1003'],
        ];
        
        foreach ($proveedores as $proveedor) {
            DB::table('proveedores')->insert($proveedor + [
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Productos de ejemplo
        $productos = [
            ['codigo' => 'ENT001', 'nombre' => 'Ensalada César', 'categoria_id' => 1, 'precio_venta' => 8.50, 'precio_compra' => 3.00, 'stock' => 50],
            ['codigo' => 'PLA001', 'nombre' => 'Filete de Res', 'categoria_id' => 2, 'precio_venta' => 25.00, 'precio_compra' => 12.00, 'stock' => 20],
            ['codigo' => 'POS001', 'nombre' => 'Tiramisú', 'categoria_id' => 3, 'precio_venta' => 7.00, 'precio_compra' => 3.00, 'stock' => 20],
            ['codigo' => 'BEB001', 'nombre' => 'Café Americano', 'categoria_id' => 4, 'precio_venta' => 3.00, 'precio_compra' => 1.00, 'stock' => 100],
            ['codigo' => 'BEB004', 'nombre' => 'Jugo Natural', 'categoria_id' => 5, 'precio_venta' => 4.00, 'precio_compra' => 1.50, 'stock' => 80],
            ['codigo' => 'COC001', 'nombre' => 'Mojito', 'categoria_id' => 6, 'precio_venta' => 8.00, 'precio_compra' => 3.00, 'stock' => 50],
            ['codigo' => 'VIN001', 'nombre' => 'Vino Tinto Reserva', 'categoria_id' => 7, 'precio_venta' => 35.00, 'precio_compra' => 15.00, 'stock' => 20],
            ['codigo' => 'CER001', 'nombre' => 'Cerveza Nacional', 'categoria_id' => 8, 'precio_venta' => 4.00, 'precio_compra' => 1.80, 'stock' => 100],
        ];
        
        foreach ($productos as $producto) {
            DB::table('productos')->insert($producto + [
                'stock_minimo' => 10,
                'unidad' => 'unidad',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
