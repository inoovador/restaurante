<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            // Agregar estado_cocina como alias de estado si no existe
            if (!Schema::hasColumn('detalle_ventas', 'estado_cocina') && Schema::hasColumn('detalle_ventas', 'estado')) {
                // No agregamos la columna, solo documentamos que usamos 'estado'
            }
            
            // Agregar observaciones si no existe (en lugar de notas)
            if (!Schema::hasColumn('detalle_ventas', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('notas');
            }
            
            // Agregar timestamps de preparación si no existen
            if (!Schema::hasColumn('detalle_ventas', 'hora_preparacion')) {
                $table->timestamp('hora_preparacion')->nullable();
            }
            
            if (!Schema::hasColumn('detalle_ventas', 'hora_entrega')) {
                $table->timestamp('hora_entrega')->nullable();
            }
            
            // Agregar índices si no existen
            $indexes = Schema::getIndexes('detalle_ventas');
            $hasVentaEstadoIndex = false;
            $hasEstadoIndex = false;
            
            foreach ($indexes as $index) {
                if (in_array('venta_id', $index['columns']) && in_array('estado', $index['columns'])) {
                    $hasVentaEstadoIndex = true;
                }
                if (in_array('estado', $index['columns']) && count($index['columns']) == 1) {
                    $hasEstadoIndex = true;
                }
            }
            
            if (!$hasVentaEstadoIndex) {
                $table->index(['venta_id', 'estado'], 'idx_venta_estado');
            }
            
            if (!$hasEstadoIndex) {
                $table->index('estado', 'idx_estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            // Eliminar columnas agregadas
            if (Schema::hasColumn('detalle_ventas', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
            
            if (Schema::hasColumn('detalle_ventas', 'hora_preparacion')) {
                $table->dropColumn('hora_preparacion');
            }
            
            if (Schema::hasColumn('detalle_ventas', 'hora_entrega')) {
                $table->dropColumn('hora_entrega');
            }
            
            // Eliminar índices
            $table->dropIndex('idx_venta_estado');
            $table->dropIndex('idx_estado');
        });
    }
};