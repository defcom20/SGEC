<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RolFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'nombre' => fake()->regexify('[A-Za-z0-9]{100}'),
            'descripcion' => fake()->text(),
        ];
    }
}
