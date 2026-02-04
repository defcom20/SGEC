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

        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('ruc', 20)->unique();
            $table->string('razon_social', 255);
            $table->string('nombre_comercial', 255)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
