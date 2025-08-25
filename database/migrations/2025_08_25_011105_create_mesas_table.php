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
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->integer('capacidad');
            $table->enum('estado', ['disponible', 'ocupada', 'reservada', 'mantenimiento'])->default('disponible');
            $table->enum('zona', ['salon_principal', 'terraza', 'barra', 'vip'])->default('salon_principal');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
