<?php

namespace Database\Factories;

use App\Models\OrdenesServicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdenServicioDetalleFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'orden_servicio_id' => OrdenesServicio::factory(),
            'descripcion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'cantidad' => fake()->randomFloat(2, 0, 99999999.99),
            'unidad_medida' => fake()->regexify('[A-Za-z0-9]{20}'),
            'precio_unitario' => fake()->randomFloat(2, 0, 99999999.99),
            'subtotal' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
