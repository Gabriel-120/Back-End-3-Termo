<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index() {
        $Estoque = \App\Models\Estoque::all(); // busca todos os estoques
        return view('Estoque.index', compact('Estoque'));
    }

    public function create() {
        $produtos = \App\Models\Produtos::all(); // para o select de produtos
        return view('estoque.create', compact('produtos'));
    }

    // Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {
        // 1. Validação simples para evitar dados vazios ou duplicados
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer',
            'localizacao' => 'nullable|string',
            'minimo' => 'nullable|integer',
            'maximo' => 'nullable|integer',
        ]);

        // 2. Salva o novo estoque
        \App\Models\Estoque::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('estoque.index')->with('success', 'Estoque cadastrado com sucesso!');
    }
}
