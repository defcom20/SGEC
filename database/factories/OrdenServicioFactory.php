<?php

namespace Database\Factories;

use App\Models\Presupuesto;
use App\Models\Subcontratista;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdenServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'numero_orden' => fake()->regexify('[A-Za-z0-9]{50}'),
            'fecha_emision' => fake()->date(),
            'presupuesto_id' => Presupuesto::factory(),
            'subcontratista_id' => Subcontratista::factory(),
            'tipo_contrato' => fake()->randomElement(["servicio_completo","solo_mano_obra"]),
            'estado' => fake()->randomElement(["pendiente","aprobado","en_ejecucion","finalizado","pagado"]),
            'fecha_aprobacion' => fake()->date(),
            'fecha_inicio_ejecucion' => fake()->date(),
            'fecha_finalizacion' => fake()->date(),
            'base_imponible' => fake()->randomFloat(2, 0, 9999999999.99),
            'igv' => fake()->randomFloat(2, 0, 9999999999.99),
            'total' => fake()->randomFloat(2, 0, 9999999999.99),
            'porcentaje_detraccion' => fake()->randomFloat(2, 0, 999.99),
            'monto_detraccion' => fake()->randomFloat(2, 0, 9999999999.99),
            'tipo_documento' => fake()->randomElement(["factura","recibo_honorarios","boleta"]),
            'observaciones' => fake()->text(),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
