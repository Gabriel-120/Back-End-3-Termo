<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

// Exemplo 1: GET
// Route::get('pokemon/{nome}', function ($nome) {
//     $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$nome}");

//     if ($response->successful()) {
//         $dados = $response->json();
//         return response()->json([
//             'status' => 'Connectado com sucesso!',
//             'resultado' => [
//                 'identificador' => $dados['id'],
//                 'nome_do_pokemon' => ucfirst($dados['name']),
//                 'foto' => $dados['sprites']['front_default']
//             ]
//         ], 200);
//     }
//     return response()->json(['erro' => 'Pokemon não encontrado'], 400);

// });

// Exemplo 2: POST
// Route::post('pokemon/novo', function (Request $request) {
//     $dados = $request->validate([
//         'nome' => 'required|string|min:3',
//         'tipo' => 'required|string',
//         'ataque' => 'required|integer'
//     ]);

//     return response()->json([
//         'mensagem' => 'Pokemon cadastrado com sucesso!',
//         'id_gerado' => rand(1000, 9999),
//         'dados_recebidos' => $dados
//     ], 201);

// });


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
                'Genero' => $dados['gender']
            ]
         ], 200);
    }
    return response()->json(['erro' => 'Usuario não Encontrado'], 400); 
});

Route::post('user/novo', function (Request $request) {
    $dados = $request->validate([
        'firstName' => 'required|string|min:3',
        'lastName' => 'required|string|min:3',
        'gender' => 'required|string|min:3',
        'age' => 'required|integer|max:2'
    ]);

    return response()->json([
        'mensagem' => 'User Cadastrado com Sucesso!',
        'id_gerado' => rand(1000, 9999),
        'dados_recebidos' => $dados
    ], 201);

});