<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'tipo_documento' => fake()->randomElement(["RUC","DNI"]),
            'numero_documento' => fake()->regexify('[A-Za-z0-9]{20}'),
            'razon_social' => fake()->regexify('[A-Za-z0-9]{255}'),
            'direccion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'distrito' => fake()->regexify('[A-Za-z0-9]{100}'),
            'provincia' => fake()->regexify('[A-Za-z0-9]{100}'),
            'departamento' => fake()->regexify('[A-Za-z0-9]{100}'),
            'persona_contacto' => fake()->regexify('[A-Za-z0-9]{150}'),
            'cargo_contacto' => fake()->regexify('[A-Za-z0-9]{100}'),
            'telefono' => fake()->regexify('[A-Za-z0-9]{20}'),
            'email' => fake()->safeEmail(),
            'estado' => fake()->randomElement(["activo","inactivo"]),
            'observaciones' => fake()->text(),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
