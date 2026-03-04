<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nossa confeccao</h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900">Lista de Fornecedores</h3>
                        </div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
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
                            <div class="inline-block bg-orange-100 text-orange-800 px-4 py-2 rounded-lg font-semibold">
                                Total: {{ isset($Fornecedores) ? $Fornecedores->count() : 0 }} fornecedor(es)
                            </div>
                        </div>
                        @if(!isset($Fornecedores) || $Fornecedores->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-600 text-lg">Nenhum fornecedor encontrado.</p>
                            </div>
                        @else
                        <table class="w-full">
                            <thead class="bg-orange-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Nome</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">CNPJ</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Telefone</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Endereço</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Ativo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($Fornecedores as $fornecedor)
                                    <tr class="hover:bg-orange-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $fornecedor->nome }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $fornecedor->cnpj }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $fornecedor->email ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $fornecedor->telefone ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $fornecedor->endereco ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-block {{ $fornecedor->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs px-3 py-1 rounded-full font-semibold">
                                                {{ $fornecedor->ativo ? 'Ativo' : 'Inativo' }}
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
