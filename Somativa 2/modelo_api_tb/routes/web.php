<?php

use App\Http\Controllers\PokemonController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pokedex/lista');

// Rotas de Pokemons
Route::get('pokedex/lista', [PokemonController::class, 'lista'])->name('pokemon.list');
Route::get('pokedex', [PokemonController::class, 'index'])->name('pokedex.show');
Route::get('pokedex/jogo', [PokemonController::class, 'gamesHub'])->name('pokemon.game');
Route::get('pokedex/jogo/quem-e-esse-pokemon', [PokemonController::class, 'guessGame'])->name('pokemon.game.guess');
Route::get('pokedex/jogo/fire-red', [PokemonController::class, 'fireRedGame'])->name('pokemon.game.fire-red');
Route::get('pokedex/criar', [PokemonController::class, 'createChoice'])->name('pokemon.create.choice');
Route::get('pokedex/criar/manual', [PokemonController::class, 'createForm'])->name('pokemon.create.form');
Route::post('pokedex/criar/manual', [PokemonController::class, 'storeManual'])->name('pokemon.store.manual');
Route::get('pokedex/fundir', [PokemonController::class, 'fusionForm'])->name('pokemon.fusion.form');
Route::post('pokedex/fundir', [PokemonController::class, 'storeFusion'])->name('pokemon.fusion.store');
Route::get('pokedex/delete', [PokemonController::class, 'deleteIndex'])->name('pokemon.delete.index');
Route::get('pokedex/delete/{id}/editar', [PokemonController::class, 'editCustomPokemon'])->name('pokemon.delete.edit');
Route::put('pokedex/delete/{id}', [PokemonController::class, 'updateCustomPokemon'])->name('pokemon.delete.update');
Route::delete('pokedex/delete', [PokemonController::class, 'deleteSelectedCustomPokemon'])->name('pokemon.delete.bulk');
Route::delete('pokedex/delete/{id}', [PokemonController::class, 'destroyCustomPokemon'])->name('pokemon.delete.destroy');
Route::post('pokedex/delete/reset-samples', [PokemonController::class, 'resetSamplePokemon'])->name('pokemon.delete.reset-samples');

Route::get('api/pokemon/search', [PokemonController::class, 'search']);
Route::get('api/pokemon/ia', [PokemonController::class, 'listarGeradosPorIA']);
Route::get('api/pokemon/tipo/{tipo}', [PokemonController::class, 'porTipo']);
Route::get('api/pokemon/lendarios', [PokemonController::class, 'lendarios']);
Route::get('api/pokemon/{id}/detalhes', [PokemonController::class, 'detalhes']);
Route::post('api/pokemon/novo', [PokemonController::class, 'criar']);
Route::put('api/pokemon/{id}', [PokemonController::class, 'atualizar']);
Route::delete('api/pokemon/{id}', [PokemonController::class, 'excluir']);

// Rotas de Usuarios
Route::get('usuarios', [UsuarioController::class, 'index']);
Route::get('api/usuarios/search', [UsuarioController::class, 'search']);

// Exemplo 1: GET
Route::get('pokemon/{nome}', function ($nome) {
    $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$nome}");

    if ($response->successful()) {
        $dados = $response->json();

        return response()->json([
            'status' => 'Conectado com sucesso!',
            'resultado' => [
                'identificador' => $dados['id'],
                'nome_do_pokemon' => ucfirst($dados['name']),
                'tipo' => $dados['types'] ?? [],
                'foto' => $dados['sprites']['front_default'] ?? null,
            ],
        ], 200);
    }

    return response()->json(['erro' => 'Pokemon nao encontrado'], 400);
});

// Exemplo 2: POST
Route::post('pokemon/novo', function (Request $request) {
    $dados = $request->validate([
        'nome' => 'required|string|min:3',
        'tipo' => 'required|string',
        'ataque' => 'required|integer',
    ]);

    return response()->json([
        'mensagem' => 'Pokemon cadastrado com sucesso!',
        'id_gerado' => rand(1000, 9999),
        'dados_recebidos' => $dados,
    ], 201);
});

Route::get('user/{id}', function ($id) {
    $response = Http::get("https://dummyjson.com/user/{$id}");

    if ($response->successful()) {
        $dados = $response->json();

        return response()->json([
            'status' => 'Conectado com Sucesso!',
            'resultado' => [
                'Identificador' => $dados['id'],
                'Primeiro Nome' => ucfirst($dados['firstName']),
                'Sobrenome' => ucfirst($dados['lastName']),
                'Idade' => $dados['age'],
                'Genero' => $dados['gender'],
            ],
        ], 200);
    }

    return response()->json(['erro' => 'Usuario nao Encontrado'], 400);
});

Route::post('user/novo', function (Request $request) {
    $dados = $request->validate([
        'firstName' => 'required|string|min:3',
        'lastName' => 'required|string|min:3',
        'gender' => 'required|string|min:3',
        'age' => 'required|integer|max:2',
    ]);

    return response()->json([
        'mensagem' => 'User Cadastrado com Sucesso!',
        'id_gerado' => rand(1000, 9999),
        'dados_recebidos' => $dados,
    ], 201);
});
