<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'ruc' => fake()->regexify('[A-Za-z0-9]{20}'),
            'razon_social' => fake()->regexify('[A-Za-z0-9]{255}'),
            'nombre_comercial' => fake()->regexify('[A-Za-z0-9]{255}'),
            'direccion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'telefono' => fake()->regexify('[A-Za-z0-9]{20}'),
            'email' => fake()->safeEmail(),
            'logo' => fake()->regexify('[A-Za-z0-9]{255}'),
        ];
    }
}
