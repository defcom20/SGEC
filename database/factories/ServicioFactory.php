<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'codigo' => fake()->regexify('[A-Za-z0-9]{50}'),
            'descripcion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'unidad_medida' => fake()->regexify('[A-Za-z0-9]{20}'),
            'precio_referencial' => fake()->randomFloat(2, 0, 99999999.99),
            'estado' => fake()->randomElement(["activo","inactivo"]),
            'usuario_creacion_id' => User::factory(),
            'usuario_modificacion_id' => User::factory(),
            'user_id' => User::factory(),
        ];
    }
}
