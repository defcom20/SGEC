<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresupuestoFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'numero_presupuesto' => fake()->regexify('[A-Za-z0-9]{50}'),
            'fecha_emision' => fake()->date(),
            'fecha_vencimiento' => fake()->date(),
            'cliente_id' => Cliente::factory(),
            'persona_contacto' => fake()->regexify('[A-Za-z0-9]{150}'),
            'supervisor_id' => User::factory(),
            'estado' => fake()->randomElement(["en_revision","aprobado","rechazado","vencido","en_ejecucion","finalizado"]),
            'fecha_aceptacion' => fake()->date(),
            'fecha_inicio' => fake()->date(),
            'fecha_finalizacion_estimada' => fake()->date(),
            'periodo_ejecucion_dias' => fake()->numberBetween(-10000, 10000),
            'base_imponible' => fake()->randomFloat(2, 0, 9999999999.99),
            'igv' => fake()->randomFloat(2, 0, 9999999999.99),
            'descuento_porcentaje' => fake()->randomFloat(2, 0, 999.99),
            'descuento_monto' => fake()->randomFloat(2, 0, 9999999999.99),
            'total' => fake()->randomFloat(2, 0, 9999999999.99),
            'observaciones' => fake()->text(),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
