<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Clientes;
use App\Models\Pedidos;
use App\Models\Fornecedores;
use App\Models\Estoque;
use App\Models\Produtos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Clientes::factory(10)->create();

        Pedidos::factory(10)->create();

        Fornecedores::factory(10)->create();

        Estoque::factory(10)->create();
        
        Produtos::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
