<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    public function index() {
        $Produtos = \App\Models\Produtos::all(); // busca todos os Produtos
        return view('Produtos.index', compact('Produtos'));
    }
}
