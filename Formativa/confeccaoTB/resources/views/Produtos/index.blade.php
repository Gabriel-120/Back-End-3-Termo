<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nossa confeccao</h2>
        <a href="{{ route('produtos.create')}}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150"> + Novo Produto</a>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de Sucesso -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm rounded-r">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900">Lista de Produtos</h3>
                        </div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Dashboard
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <div class="mb-6 flex items-center justify-between">
                            <div class="inline-block bg-purple-100 text-purple-800 px-4 py-2 rounded-lg font-semibold">
                                Total: {{ isset($Produtos) ? $Produtos->count() : 0 }} produto(s)
                            </div>
                        </div>
                        @if(!isset($Produtos) || $Produtos->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-600 text-lg">Nenhum produto encontrado.</p>
                            </div>
                        @else
                        <table class="w-full">
                            <thead class="bg-purple-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Nome</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Descrição</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">SKU</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Preço</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Categoria</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Estoque Mínimo</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Ativo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($Produtos as $produto)
                                    <tr class="hover:bg-purple-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $produto->nome }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $produto->descricao ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $produto->sku }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $produto->categoria ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $produto->estoque_minimo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-block {{ $produto->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs px-3 py-1 rounded-full font-semibold">
                                                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
