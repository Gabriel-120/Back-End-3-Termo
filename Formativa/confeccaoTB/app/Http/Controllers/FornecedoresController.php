<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FornecedoresController extends Controller
{
    public function index() {
        $Fornecedores = \App\Models\Fornecedores::all(); // busca todos os fornecedores
        return view('Fornecedores.index', compact('Fornecedores'));
    }

    public function create() {
        return view('fornecedores.create');
    }

    // Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {
        // 1. Validação simples para evitar dados vazios ou duplicados
        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:fornecedores',
            'email' => 'required|email|unique:fornecedores',
            'telefone' => 'required|string',
            'endereco' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        // 2. Salva o novo fornecedor
        \App\Models\Fornecedores::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor cadastrado com sucesso!');
    }
}
