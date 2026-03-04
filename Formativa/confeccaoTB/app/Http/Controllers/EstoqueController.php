<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index() {
        $Estoque = \App\Models\Estoque::all(); // busca todos os pedidos
        return view('Estoque.index', compact('Estoque'));
    }
}
