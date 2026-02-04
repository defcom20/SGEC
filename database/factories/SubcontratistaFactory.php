<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubcontratistaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'tipo' => fake()->randomElement(["empresa","persona_natural"]),
            'tipo_documento' => fake()->randomElement(["RUC","DNI"]),
            'numero_documento' => fake()->regexify('[A-Za-z0-9]{20}'),
            'razon_social_nombre' => fake()->regexify('[A-Za-z0-9]{255}'),
            'direccion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'telefono' => fake()->regexify('[A-Za-z0-9]{20}'),
            'email' => fake()->safeEmail(),
            'banco' => fake()->regexify('[A-Za-z0-9]{100}'),
            'numero_cuenta' => fake()->regexify('[A-Za-z0-9]{50}'),
            'cci' => fake()->regexify('[A-Za-z0-9]{50}'),
            'numero_cuenta_detraccion' => fake()->regexify('[A-Za-z0-9]{50}'),
            'estado' => fake()->randomElement(["activo","inactivo"]),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
