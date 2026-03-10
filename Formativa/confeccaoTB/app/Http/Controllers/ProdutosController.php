<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    public function index() {
        $Produtos = \App\Models\Produtos::all(); // busca todos os Produtos
        return view('Produtos.index', compact('Produtos'));
    }

    public function create() {
        return view('produtos.create');
    }

    // Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {
        // 1. Validação simples para evitar dados vazios ou duplicados
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'sku' => 'required|string|unique:produtos',
            'preco' => 'required|numeric',
            'categoria' => 'nullable|string',
            'estoque_minimo' => 'nullable|integer',
            'ativo' => 'boolean',
        ]);

        // 2. Salva o novo produto
        \App\Models\Produtos::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('produtos.index')->with('success', 'Produto cadastrado com sucesso!');
    }
}
