<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\FornecedoresController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ProdutosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index')->middleware('auth');

Route::get('/pedidos', [PedidosController::class, 'index'])->name('pedidos.index')->middleware('auth');

Route::get('/fornecedores', [FornecedoresController::class, 'index'])->name('fornecedores.index')->middleware('auth');

Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index')->middleware('auth');

Route::get('/produtos', [ProdutosController::class, 'index'])->name('produtos.index')->middleware('auth');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
