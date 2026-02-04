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

        Schema::create('orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('numero_orden', 50)->unique();
            $table->date('fecha_emision');
            $table->foreignId('presupuesto_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('subcontratista_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('tipo_contrato', ["servicio_completo","solo_mano_obra"])->default('servicio_completo');
            $table->enum('estado', ["pendiente","aprobado","en_ejecucion","finalizado","pagado"])->default('pendiente');
            $table->date('fecha_aprobacion')->nullable();
            $table->date('fecha_inicio_ejecucion')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->decimal('base_imponible', 12, 2);
            $table->decimal('igv', 12, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('porcentaje_detraccion', 5, 2)->nullable();
            $table->decimal('monto_detraccion', 12, 2)->nullable();
            $table->enum('tipo_documento', ["factura","recibo_honorarios","boleta"])->default('factura');
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
        Schema::dropIfExists('orden_servicios');
    }
};
