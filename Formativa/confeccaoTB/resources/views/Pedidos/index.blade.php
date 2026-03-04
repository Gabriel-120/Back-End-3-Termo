<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nossa confeccao</h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900">Lista de Pedidos</h3>
                        </div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
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
                            <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">
                                Total: {{ isset($Pedidos) ? $Pedidos->count() : 0 }} pedido(s)
                            </div>
                        </div>
                        @if(!isset($Pedidos) || $Pedidos->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-600 text-lg">Nenhum pedido encontrado.</p>
                            </div>
                        @else
                        <table class="w-full">
                            <thead class="bg-green-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Número do Pedido</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Valor Total</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Método de Pagamento</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Observações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($Pedidos as $pedido)
                                    <tr class="hover:bg-green-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $pedido->numero_pedido }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-semibold">{{ $pedido->status }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $pedido->metodo_pagamento ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $pedido->observacoes ?? 'N/A' }}</td>
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
