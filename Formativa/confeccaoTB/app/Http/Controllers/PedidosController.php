<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function index() {
        $Pedidos = \App\Models\Pedidos::all(); // busca todos os pedidos
        return view('Pedidos.index', compact('Pedidos'));
    }
}
