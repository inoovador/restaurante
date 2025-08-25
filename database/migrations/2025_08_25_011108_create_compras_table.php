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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compra')->unique();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('usuario_id')->constrained('users');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'recibido', 'cancelado'])->default('pendiente');
            $table->date('fecha_recepcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
