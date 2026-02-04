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

        Schema::create('orden_servicio_detalles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('descripcion', 255);
            $table->decimal('cantidad', 10, 2);
            $table->string('unidad_medida', 20);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_servicio_detalles');
    }
};
