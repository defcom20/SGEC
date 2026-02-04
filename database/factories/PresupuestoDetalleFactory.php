<?php

namespace Database\Factories;

use App\Models\Presupuesto;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresupuestoDetalleFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'presupuesto_id' => Presupuesto::factory(),
            'servicio_id' => Servicio::factory(),
            'descripcion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'cantidad' => fake()->randomFloat(2, 0, 99999999.99),
            'unidad_medida' => fake()->regexify('[A-Za-z0-9]{20}'),
            'precio_unitario' => fake()->randomFloat(2, 0, 99999999.99),
            'subtotal' => fake()->randomFloat(2, 0, 9999999999.99),
            'orden' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
