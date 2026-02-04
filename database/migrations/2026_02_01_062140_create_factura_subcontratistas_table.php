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

        Schema::create('factura_subcontratistas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->enum('tipo_documento', ["factura","recibo_honorarios","boleta"])->default('factura');
            $table->string('numero_documento', 50);
            $table->string('serie', 20)->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->foreignId('subcontratista_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('orden_servicio_id')->constrained('ordenes_servicios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('base_imponible', 12, 2);
            $table->decimal('igv', 12, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('porcentaje_detraccion', 5, 2)->nullable();
            $table->decimal('monto_detraccion', 12, 2)->nullable();
            $table->enum('estado', ["pendiente","pago_parcial","pagado_completo"])->default('pendiente');
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('monto_pendiente', 12, 2);
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('factura_subcontratistas');
    }
};
