<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produtos>
 */
class ProdutosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'              => fake()->word(),
            'descricao'         => fake()->sentence(),
            'sku'               => fake()->unique()->numerify('SKU-####'),
            'preco'             => fake()->randomFloat(2, 10, 500),
            'categoria'         => fake()->randomElement(['Camisetas', 'Calças', 'Jaquetas', 'Acessórios']),
            'estoque_minimo'    => fake()->numberBetween(5, 50),
            'ativo'             => fake()->boolean(90),
        ];
    }
}
