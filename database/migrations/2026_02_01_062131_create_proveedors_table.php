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

        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->enum('tipo_documento', ["RUC"])->default('RUC');
            $table->string('numero_documento', 20)->unique();
            $table->string('razon_social', 255);
            $table->string('rubro', 150)->nullable();
            $table->string('contacto', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
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
        Schema::dropIfExists('proveedors');
    }
};
