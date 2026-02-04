<?php

namespace Database\Factories;

use App\Models\FacturasSubcontratista;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PagoSubcontratistaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'factura_subcontratista_id' => FacturasSubcontratista::factory(),
            'numero_pago' => fake()->regexify('[A-Za-z0-9]{50}'),
            'fecha_pago' => fake()->date(),
            'monto' => fake()->randomFloat(2, 0, 9999999999.99),
            'metodo_pago' => fake()->randomElement(["efectivo","transferencia","cheque","deposito"]),
            'banco' => fake()->regexify('[A-Za-z0-9]{100}'),
            'numero_operacion' => fake()->regexify('[A-Za-z0-9]{100}'),
            'cuenta_detraccion_usada' => fake()->boolean(),
            'comprobante' => fake()->regexify('[A-Za-z0-9]{255}'),
            'observaciones' => fake()->text(),
            'usuario_registro_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
