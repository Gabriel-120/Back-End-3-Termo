<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clientes>
 */
class ClientesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'nome' => fake()->firstName() . ' ' . fake()->lastName(),
            'cpf' => fake()->numerify('###########'),
            'email' => fake()->email(),
            'telefone' => fake()->numerify('(##) #####-####'),
            'endereco' => fake()->address(),
        ];
    }
}
