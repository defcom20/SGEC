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

        Schema::create('pago_clientes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('factura_cliente_id')->constrained('facturas_clientes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('numero_pago', 50);
            $table->date('fecha_pago');
            $table->decimal('monto', 12, 2);
            $table->enum('metodo_pago', ["efectivo","transferencia","cheque","deposito"])->default('transferencia');
            $table->string('banco', 100)->nullable();
            $table->string('numero_operacion', 100)->nullable();
            $table->string('comprobante', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_registro_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_clientes');
    }
};
