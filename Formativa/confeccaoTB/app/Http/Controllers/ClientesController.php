<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Models\Clientes;

class ClientesController extends Controller
{
    public function index() {
        $Clientes = Clientes::all(); // busca todos os clientes
        return view('Clientes.index', compact('Clientes'));
    }

    public function create() {
        return view('clientes.create');
    }

    // Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {
        // 1. Validação simples para evitar dados vazios ou duplicados
        $request->validate([
            'nome'      => 'required|string|max:255',
            'cpf'       => 'required|string|unique:clientes',
            'email'     => 'required|email|unique:clientes',
            'telefone'  => 'required|string',
            'endereco'  => 'nullable|string',
        ]);

        // 2. Salva o novo cliente
        Clientes::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    // abre a tela de edicao
    public function edit(Clientes $cliente) {
        return view('clientes.edit', ['Clientes' => $cliente]);
    }

    // Salva alrteracao no banco
    public function update(Request $request, Clientes $cliente) {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|unique:Clientes,cpf,' . $cliente->id,
            'email' => 'required|email|unique:Clientes,email,' . $cliente->id,
            'telefone' => 'required',
            'endereco' => 'string',
        ]);

        $cliente->update($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado!');
    }

    // Excluir clientes
    public function destroy(Clientes $cliente) {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente removido!');
    }
}
