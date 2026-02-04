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

        Schema::create('subcontratistas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->enum('tipo', ["empresa","persona_natural"])->default('empresa');
            $table->enum('tipo_documento', ["RUC","DNI"])->default('RUC');
            $table->string('numero_documento', 20)->unique();
            $table->string('razon_social_nombre', 255);
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('banco', 100)->nullable();
            $table->string('numero_cuenta', 50)->nullable();
            $table->string('cci', 50)->nullable();
            $table->string('numero_cuenta_detraccion', 50)->nullable();
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
        Schema::dropIfExists('subcontratistas');
    }
};
