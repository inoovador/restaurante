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
        // Add activo column to roles table
        if (!Schema::hasColumn('roles', 'activo')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('activo')->default(true)->after('descripcion');
            });
        }

        // Add fecha column to compras table
        if (!Schema::hasColumn('compras', 'fecha')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->date('fecha')->nullable()->after('id');
            });
        }

        // Create proveedores table if it doesn't exist
        if (!Schema::hasTable('proveedores')) {
            Schema::create('proveedores', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('ruc')->unique();
                $table->string('direccion')->nullable();
                $table->string('telefono')->nullable();
                $table->string('email')->nullable();
                $table->string('contacto')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Create ventas_detalle table if it doesn't exist
        if (!Schema::hasTable('ventas_detalle')) {
            Schema::create('ventas_detalle', function (Blueprint $table) {
                $table->id();
                $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
                $table->foreignId('producto_id')->constrained('productos');
                $table->integer('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->text('observaciones')->nullable();
                $table->enum('estado_cocina', ['pendiente', 'preparando', 'listo', 'entregado'])->default('pendiente');
                $table->timestamp('hora_preparacion')->nullable();
                $table->timestamp('hora_entrega')->nullable();
                $table->timestamps();

                $table->index(['venta_id', 'estado_cocina']);
                $table->index('estado_cocina');
            });
        }

        // Add tiempo_preparacion to productos if it doesn't exist
        if (!Schema::hasColumn('productos', 'tiempo_preparacion')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->integer('tiempo_preparacion')->nullable()->after('stock_minimo')->comment('Tiempo en minutos');
            });
        }

        // Add area column to categorias if it doesn't exist
        if (!Schema::hasColumn('categorias', 'area')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->enum('area', ['cocina', 'barra'])->default('cocina')->after('tipo');
            });
        }

        // Add columns to compras table
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (!Schema::hasColumn('compras', 'proveedor_id')) {
                    $table->foreignId('proveedor_id')->nullable()->after('fecha')->constrained('proveedores');
                }
                if (!Schema::hasColumn('compras', 'numero_factura')) {
                    $table->string('numero_factura')->nullable()->after('proveedor_id');
                }
                if (!Schema::hasColumn('compras', 'observaciones')) {
                    $table->text('observaciones')->nullable()->after('estado');
                }
            });
        }

        // Add columns to ventas table
        if (!Schema::hasColumn('ventas', 'tipo_pedido')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->enum('tipo_pedido', ['local', 'llevar', 'delivery'])->default('local')->after('mesa_id');
            });
        }

        // Add columns to mesas table
        if (!Schema::hasColumn('mesas', 'zona')) {
            Schema::table('mesas', function (Blueprint $table) {
                $table->string('zona')->nullable()->after('capacidad');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_detalle');
        Schema::dropIfExists('proveedores');

        if (Schema::hasColumn('roles', 'activo')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }

        if (Schema::hasColumn('compras', 'fecha')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->dropColumn('fecha');
            });
        }

        if (Schema::hasColumn('compras', 'proveedor_id')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->dropForeign(['proveedor_id']);
                $table->dropColumn(['proveedor_id', 'numero_factura', 'observaciones']);
            });
        }

        if (Schema::hasColumn('productos', 'tiempo_preparacion')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('tiempo_preparacion');
            });
        }

        if (Schema::hasColumn('categorias', 'area')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->dropColumn('area');
            });
        }

        if (Schema::hasColumn('ventas', 'tipo_pedido')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->dropColumn('tipo_pedido');
            });
        }

        if (Schema::hasColumn('mesas', 'zona')) {
            Schema::table('mesas', function (Blueprint $table) {
                $table->dropColumn('zona');
            });
        }
    }
};
