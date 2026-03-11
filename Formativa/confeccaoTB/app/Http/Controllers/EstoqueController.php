<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Estoque;
use \App\Models\Produtos;

class EstoqueController extends Controller
{
    public function index() {
        $Estoque = Estoque::all(); // busca todos os estoques
        return view('Estoque.index', compact('Estoque'));
    }

    public function create() {
        $produtos = Produtos::all(); // para o select de produtos
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
        Estoque::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('estoque.index')->with('success', 'Estoque cadastrado com sucesso!');
    }

    public function edit(Estoque $estoque) {
        return view('estoque.edit', ['Estoque' => $estoque]);
    }

    public function update(Request $request, Estoque $estoque) {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id' . $estoque->id,
            'quantidade' => 'required|integer',
            'localizacao' => 'nullable|string',
            'minimo' => 'nullable|integer',
            'maximo' => 'nullable|integer',
        ]);

        $estoque->update($request->all());
        return redirect()->route('estoque.index')->with('success','estoque atualizado!');
    }

    public function destroy(Estoque $estoque) {
        $estoque->delete();
        return redirect()->route('estoque.index')->with('success','Estoque removido!');
    }
}
