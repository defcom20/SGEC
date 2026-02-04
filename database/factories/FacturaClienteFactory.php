<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Presupuesto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacturaClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'numero_factura' => fake()->regexify('[A-Za-z0-9]{50}'),
            'serie' => fake()->regexify('[A-Za-z0-9]{20}'),
            'fecha_emision' => fake()->date(),
            'fecha_vencimiento' => fake()->date(),
            'cliente_id' => Cliente::factory(),
            'presupuesto_id' => Presupuesto::factory(),
            'base_imponible' => fake()->randomFloat(2, 0, 9999999999.99),
            'igv' => fake()->randomFloat(2, 0, 9999999999.99),
            'descuento_porcentaje' => fake()->randomFloat(2, 0, 999.99),
            'descuento_descripcion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'descuento_monto' => fake()->randomFloat(2, 0, 9999999999.99),
            'total' => fake()->randomFloat(2, 0, 9999999999.99),
            'porcentaje_detraccion' => fake()->randomFloat(2, 0, 999.99),
            'monto_detraccion' => fake()->randomFloat(2, 0, 9999999999.99),
            'estado' => fake()->randomElement(["pendiente","pago_parcial","pagado_completo"]),
            'monto_pagado' => fake()->randomFloat(2, 0, 9999999999.99),
            'monto_pendiente' => fake()->randomFloat(2, 0, 9999999999.99),
            'observaciones' => fake()->text(),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
