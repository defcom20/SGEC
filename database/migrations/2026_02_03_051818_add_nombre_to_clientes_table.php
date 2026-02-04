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
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'nombre')) {
                $table->string('nombre')->after('numero_documento');
            }
            if (!Schema::hasColumn('clientes', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('clientes', 'direccion')) {
                $table->text('direccion')->nullable()->after('telefono');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'nombre')) {
                $table->dropColumn('nombre');
            }
            if (Schema::hasColumn('clientes', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('clientes', 'direccion')) {
                $table->dropColumn('direccion');
            }
        });
    }
};
