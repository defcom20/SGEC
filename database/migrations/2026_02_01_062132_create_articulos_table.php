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
        Schema::disableForeignKeyConstraints();

        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('codigo', 50)->unique();
            $table->string('descripcion', 255);
            $table->string('unidad_medida', 20);
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('stock', 10, 2)->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ["activo","inactivo"])->default('activo');
            $table->foreignId('usuario_creacion_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('usuario_modificacion_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
