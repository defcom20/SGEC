<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ParametroFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'clave' => fake()->regexify('[A-Za-z0-9]{100}'),
            'valor' => fake()->text(),
            'descripcion' => fake()->regexify('[A-Za-z0-9]{255}'),
            'tipo_dato' => fake()->randomElement(["decimal","string","integer","boolean"]),
        ];
    }
}
