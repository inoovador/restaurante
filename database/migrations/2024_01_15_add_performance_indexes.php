<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para la tabla productos
        Schema::table('productos', function (Blueprint $table) {
            $table->index('activo', 'idx_productos_activo');
            $table->index('categoria_id', 'idx_productos_categoria');
            $table->index(['activo', 'categoria_id'], 'idx_productos_activo_categoria');
            $table->index(['nombre', 'activo'], 'idx_productos_nombre_activo');
            $table->index('created_at', 'idx_productos_created_at');
            $table->index('stock', 'idx_productos_stock');
        });

        // Índices para la tabla categorias
        Schema::table('categorias', function (Blueprint $table) {
            $table->index('activo', 'idx_categorias_activo');
            $table->index('nombre', 'idx_categorias_nombre');
        });

        // Índices para la tabla ventas
        Schema::table('ventas', function (Blueprint $table) {
            $table->index('created_at', 'idx_ventas_created_at');
            $table->index('estado', 'idx_ventas_estado');
            $table->index(['created_at', 'estado'], 'idx_ventas_fecha_estado');
            $table->index('cliente_id', 'idx_ventas_cliente');
            $table->index('mesa_id', 'idx_ventas_mesa');
        });

        // Índices para la tabla mesas
        Schema::table('mesas', function (Blueprint $table) {
            $table->index('estado', 'idx_mesas_estado');
            $table->index('zona', 'idx_mesas_zona');
            $table->index(['estado', 'zona'], 'idx_mesas_estado_zona');
        });

        // Índices para detalle_ventas si existe
        if (Schema::hasTable('detalle_ventas')) {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->index('venta_id', 'idx_detalle_venta');
                $table->index('producto_id', 'idx_detalle_producto');
                $table->index('created_at', 'idx_detalle_created_at');
                $table->index(['producto_id', 'created_at'], 'idx_detalle_producto_fecha');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_activo');
            $table->dropIndex('idx_productos_categoria');
            $table->dropIndex('idx_productos_activo_categoria');
            $table->dropIndex('idx_productos_nombre_activo');
            $table->dropIndex('idx_productos_created_at');
            $table->dropIndex('idx_productos_stock');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropIndex('idx_categorias_activo');
            $table->dropIndex('idx_categorias_nombre');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('idx_ventas_created_at');
            $table->dropIndex('idx_ventas_estado');
            $table->dropIndex('idx_ventas_fecha_estado');
            $table->dropIndex('idx_ventas_cliente');
            $table->dropIndex('idx_ventas_mesa');
        });

        Schema::table('mesas', function (Blueprint $table) {
            $table->dropIndex('idx_mesas_estado');
            $table->dropIndex('idx_mesas_zona');
            $table->dropIndex('idx_mesas_estado_zona');
        });

        if (Schema::hasTable('detalle_ventas')) {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->dropIndex('idx_detalle_venta');
                $table->dropIndex('idx_detalle_producto');
                $table->dropIndex('idx_detalle_created_at');
                $table->dropIndex('idx_detalle_producto_fecha');
            });
        }
    }
};