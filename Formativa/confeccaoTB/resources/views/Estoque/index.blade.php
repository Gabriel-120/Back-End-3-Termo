<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nossa confeccao</h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900">Lista de Estoques</h3>
                        </div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
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
                            <div class="inline-block bg-red-100 text-red-800 px-4 py-2 rounded-lg font-semibold">
                                Total: {{ isset($Estoque) ? $Estoque->count() : 0 }} estoque(s)
                            </div>
                        </div>
                        @if(!isset($Estoque) || $Estoque->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-600 text-lg">Nenhum estoque encontrado.</p>
                            </div>
                        @else
                        <table class="w-full">
                            <thead class="bg-red-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Produto ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Quantidade</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Localização</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Mínimo</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Máximo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($Estoque as $estoque)
                                    <tr class="hover:bg-red-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $estoque->produto_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $estoque->quantidade }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $estoque->localizacao ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $estoque->minimo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $estoque->maximo ?? 'N/A' }}</td>
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
