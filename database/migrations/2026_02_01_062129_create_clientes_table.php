<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('tipo_persona')->default('JURIDICA'); // NATURAL, JURIDICA
            $table->string('tipo_documento'); // RUC, DNI, ETC
            $table->string('numero_documento')->index();
            $table->string('nombre'); // Razón Social o Nombres y Apellidos

            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();

            $table->boolean('estado')->default(true); // true = Activo, false = Inactivo

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
