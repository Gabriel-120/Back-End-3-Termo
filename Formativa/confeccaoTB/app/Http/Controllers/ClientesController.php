<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index() {
        $Clientes = \App\Models\Clientes::all(); // busca todos os clientes
        return view('Clientes.index', compact('Clientes'));
    }
}
