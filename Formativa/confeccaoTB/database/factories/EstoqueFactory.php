<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estoque>
 */
class EstoqueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'produto_id'   => fake()->numberBetween(1, 15),
            'quantidade'   => fake()->numberBetween(0, 200),
            'localizacao'  => fake()->word(),
            'minimo'       => fake()->numberBetween(0, 50),
            'maximo'       => fake()->numberBetween(50, 500),
        ];
    }
}
