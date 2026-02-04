<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
            }
            if (!Schema::hasColumn('clientes', 'tipo_persona')) {
                $table->string('tipo_persona')->default('JURIDICA')->after('uuid');
            }
            if (!Schema::hasColumn('clientes', 'tipo_documento')) {
                $table->string('tipo_documento')->nullable()->after('tipo_persona');
            }
            if (!Schema::hasColumn('clientes', 'numero_documento')) {
                $table->string('numero_documento')->nullable()->index()->after('tipo_documento');
            }
            if (!Schema::hasColumn('clientes', 'email') && !Schema::hasColumn('clientes', 'correo')) {
                $table->string('email')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('clientes', 'estado')) {
                $table->boolean('estado')->default(true)->after('direccion');
            }
            if (!Schema::hasColumn('clientes', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Llenar UUIDs vacíos si existen registros
        if (Schema::hasColumn('clientes', 'uuid')) {
            $clientesSinUuid = DB::table('clientes')->whereNull('uuid')->get();
            foreach ($clientesSinUuid as $cliente) {
                DB::table('clientes')->where('id', $cliente->id)->update(['uuid' => (string) Str::uuid()]);
            }

            // Hacer uuid not null y unique si ya se llenaron
            Schema::table('clientes', function (Blueprint $table) {
                // $table->uuid('uuid')->nullable(false)->change(); // A veces falla en algunos drivers si ya es null
                // $table->unique('uuid'); 
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // No drop columns to avoid data loss in rollback unless strictly necessary
            if (Schema::hasColumn('clientes', 'uuid'))
                $table->dropColumn('uuid');
            if (Schema::hasColumn('clientes', 'tipo_persona'))
                $table->dropColumn('tipo_persona');
            if (Schema::hasColumn('clientes', 'deleted_at'))
                $table->dropSoftDeletes();
        });
    }
};
