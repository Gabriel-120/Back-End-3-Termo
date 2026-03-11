<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Produtos;

class ProdutosController extends Controller
{
    public function index() {
        $Produtos = Produtos::all(); // busca todos os Produtos
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
        Produtos::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('produtos.index')->with('success', 'Produto cadastrado com sucesso!');
    }

    // abre edicao
    public function edit(Produtos $produtos) {
        return view('produtos.edit', ['Produtos' => $produtos]);
    }

    public function update(Request $request, Produtos $produtos) {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'sku' => 'required|string|unique:produtos,sku' . $produtos->id,
            'preco' => 'required|numeric',
            'categoria' => 'nullable|string',
            'estoque_minimo' => 'nullable|integer',
            'ativo' => 'boolean',
        ]);

        $produtos->update($request->all());
        return redirect()->route('produtos.index')->with('success', 'Produtos atualizados');
    }

    public function destroy(Produtos $produtos) {
        $produtos->delete();
        return redirect()->route('produtos.index')->with('success','Produto removido!');
    }
}
