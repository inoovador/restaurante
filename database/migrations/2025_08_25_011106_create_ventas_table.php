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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta')->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('mesa_id')->nullable()->constrained('mesas');
            $table->foreignId('usuario_id')->constrained('users');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            $table->enum('tipo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto'])->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
