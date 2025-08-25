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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->decimal('monto_apertura', 10, 2);
            $table->decimal('monto_cierre', 10, 2)->nullable();
            $table->decimal('ventas_efectivo', 10, 2)->default(0);
            $table->decimal('ventas_tarjeta', 10, 2)->default(0);
            $table->decimal('gastos', 10, 2)->default(0);
            $table->decimal('ingresos_extra', 10, 2)->default(0);
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->datetime('fecha_apertura');
            $table->datetime('fecha_cierre')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
