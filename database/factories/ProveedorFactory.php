<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'tipo_documento' => fake()->randomElement(["RUC"]),
            'numero_documento' => fake()->regexify('[A-Za-z0-9]{20}'),
            'razon_social' => fake()->regexify('[A-Za-z0-9]{255}'),
            'rubro' => fake()->regexify('[A-Za-z0-9]{150}'),
            'contacto' => fake()->regexify('[A-Za-z0-9]{150}'),
            'telefono' => fake()->regexify('[A-Za-z0-9]{20}'),
            'email' => fake()->safeEmail(),
            'estado' => fake()->randomElement(["activo","inactivo"]),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
