<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fornecedores>
 */
class FornecedoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'      => fake()->company(),
            'cnpj'      => fake()->unique()->numerify('###########'), // 14 dígitos se quiser formatar
            'email'     => fake()->companyEmail(),
            'telefone'  => fake()->phoneNumber(),
            'endereco'  => fake()->address(),
            'ativo'     => fake()->boolean(90),
        ];
    }
}
