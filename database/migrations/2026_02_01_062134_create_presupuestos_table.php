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

        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('numero_presupuesto', 50)->unique();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('persona_contacto', 150)->nullable();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('estado', ["en_revision","aprobado","rechazado","vencido","en_ejecucion","finalizado"])->default('en_revision');
            $table->date('fecha_aceptacion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_finalizacion_estimada')->nullable();
            $table->integer('periodo_ejecucion_dias')->nullable();
            $table->decimal('base_imponible', 12, 2);
            $table->decimal('igv', 12, 2);
            $table->decimal('descuento_porcentaje', 5, 2)->nullable();
            $table->decimal('descuento_monto', 12, 2)->nullable();
            $table->decimal('total', 12, 2);
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
        Schema::dropIfExists('presupuestos');
    }
};
