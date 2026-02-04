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
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->string('nombre', 100);
            $table->string('slug', 100)->unique();
            $table->string('icono', 50)->nullable();
            $table->string('ruta', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('visible_menu')->default(true);

            // Jerarquía de módulos
            $table->foreignId('parent_id')->nullable()->constrained('modulos')->onDelete('cascade');
            $table->tinyInteger('nivel')->default(1)->comment('1=padre, 2=hijo');

            $table->json('acciones')->nullable(); // ['ver', 'crear', 'editar', 'eliminar']
            $table->foreignId('usuario_creacion_id')->nullable()->constrained('users');
            $table->foreignId('usuario_modificacion_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
