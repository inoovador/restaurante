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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'devolucion', 'merma']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->decimal('costo_total', 10, 2)->nullable();
            $table->string('motivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('documento_referencia')->nullable(); // Número de factura, guía, etc.
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->foreignId('compra_id')->nullable(); // Sin constraint por ahora
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index('producto_id');
            $table->index('tipo_movimiento');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
