<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function index() {
        $Pedidos = \App\Models\Pedidos::all(); // busca todos os pedidos
        return view('Pedidos.index', compact('Pedidos'));
    }

    public function create() {
        return view('pedidos.create');
    }

    // Recebe os dados do formulario e salva no banco de dados
    public function store(Request $request) {
        // 1. Validação simples para evitar dados vazios ou duplicados
        $request->validate([
            'numero_pedido' => 'required|string|unique:pedidos',
            'valor_total' => 'required|numeric',
            'status' => 'required|string',
            'metodo_pagamento' => 'required|string',
            'observacoes' => 'nullable|string',
        ]);

        // 2. Salva o novo pedido
        \App\Models\Pedidos::create($request->all());

        // 3. redirect de volta para a lista com uma mensagem de sucesso
        return redirect()->route('pedidos.index')->with('success', 'Pedido cadastrado com sucesso!');
    }
}
