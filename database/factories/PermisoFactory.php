<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PermisoFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'modulo' => fake()->regexify('[A-Za-z0-9]{100}'),
            'accion' => fake()->regexify('[A-Za-z0-9]{50}'),
            'descripcion' => fake()->regexify('[A-Za-z0-9]{255}'),
        ];
    }
}
