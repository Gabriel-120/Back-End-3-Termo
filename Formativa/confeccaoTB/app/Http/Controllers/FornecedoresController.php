<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Fornecedores;

class FornecedoresController extends Controller
{
    public function index()
    {
        $Fornecedores = Fornecedores::all(); // busca todos os fornecedores
        return view('Fornecedores.index', compact('Fornecedores'));
    }

    public function create()
    {
        return view('fornecedores.create');
    }

    // Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request)
    {
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
        Fornecedores::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('fornecedores.index')->with('success', 'Fornecedor cadastrado com sucesso!');
    }

    // tela edicao
    public function edit(Fornecedores $fornecedores)
    {
        return view('fornecedores.edit', ['Fornecedores' => $fornecedores]);
    }


    // salva alteracao
    public function update(Request $request, Fornecedores $fornecedores) {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:fornecedores,cnpj' . $fornecedores->id,
            'email' => 'required|email|unique:fornecedores,email' . $fornecedores->id,
            'telefone' => 'required|string',
            'endereco' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $fornecedores->update($request->all());
        return redirect()->route('fornecedores.index')->with('success','Fornecedor atualizado!');
    }

    // excluir Fornecedores
    public function destroy(Fornecedores $fornecedores) {
        $fornecedores->delete();
        return redirect()->route('fornecedores.edit')->with('success', 'fornecedor removido!');
    }
}
