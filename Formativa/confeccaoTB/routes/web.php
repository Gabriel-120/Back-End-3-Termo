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





// novas rotas da aula do dia 10/03
Route::get('/clientes/create', [ClientesController::class, 'create'])->name('clientes.create');

// rotas para editar dados do banco
Route::get('/clientes/{cliente}/edit', [ClientesController::class, 'edit'])->name('clientes.edit')->middleware('auth');
Route::put('/clientes/{cliente}', [ClientesController::class, 'update'])->name('clientes.update')->middleware('auth');
Route::delete('/clientes/{cliente}', [ClientesController::class, 'destroy'])->name('clientes.destroy')->middleware('auth');

// Rota para RECEBER os dados e salvar (POST)
Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');


Route::get('/pedidos/create', [PedidosController::class, 'create'])->name('pedidos.create');
// rotas para editar dados do banco
Route::get('/pedidos/{pedidos}/edit', [PedidosController::class, 'edit'])->name('pedidos.edit')->middleware('auth');
Route::put('/pedidos/{pedidos}', [PedidosController::class,'update'])->name('pedidos.update')->middleware('auth');
Route::delete('/pedidos/{pedidos}', [PedidosController::class,'destroy'])->name('pedidos.destroy')->middleware('auth');

// rota para RECEBER os dados e salvar (POST)
Route::post('/pedidos', [PedidosController::class, 'store'])->name('pedidos.store');


// PRODUTOS
Route::get('/produtos/create', [ProdutosController::class, 'create'])->name('produtos.create');
// rotas para editar dados do banco
Route::get('/produtos/{produtos}/edit', [ProdutosController::class,'edit'])->name('produtos.edit')->middleware('auth');
Route::put('/produtos/{produtos}', [ProdutosController::class,'update'])->name('produtos.update')->middleware('auth');
Route::delete('/produtos/{produtos}', [ProdutosController::class,'destroy'])->name('produtos.destroy')->middleware('auth');

// rota para RECEBER os dados e salvar (POST)
Route::post('/produtos', [ProdutosController::class, 'store'])->name('produtos.store');



// FORNECEDORES
Route::get('/fornecedores/create', [FornecedoresController::class, 'create'])->name('fornecedores.create');
// rotas para editar dados do banco
Route::get('/fornecedores/{fornecedores}/edit', [FornecedoresController::class, 'edit'])->name('fornecedores.edit')->middleware('auth');
Route::put('/fornecedores/{fornecedores}', [FornecedoresController::class,'update'])->name('fornecedores.update')->middleware('auth');
Route::delete('/fornecedores/{fornecedores}', [FornecedoresController::class, 'destroy'])->name('fornecedores.destroy')->middleware('auth');

// rota para RECEBER os dados e salvar (POST)
Route::post('/fornecedores', [FornecedoresController::class, 'store'])->name('fornecedores.store');


// ESTOQUE
Route::get('/estoque/create', [EstoqueController::class, 'create'])->name('estoque.create');
// rotas para editar dados do banco
Route::get('/estoque/{estoque}/edit', [EstoqueController::class, 'edit'])->name('estoque.edit')->middleware('auth');
route::put('/estoque/{estoque}', [EstoqueController::class,'update'])->name('estoque.update')->middleware('auth');
Route::delete('/estoque/{estoque}', [EstoqueController::class, 'destroy'])->name('estoque.destroy')->middleware('auth');

// rota para RECEBER os dados e salvar (POST)
Route::post('/estoque', [EstoqueController::class, 'store'])->name('estoque.store');



// rotas do gerais das aulas passadas
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
