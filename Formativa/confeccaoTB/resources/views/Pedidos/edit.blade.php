<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Pedido</h2>
            </div>
            <a href="{{ route('pedidos.index') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <!-- Header com gradient -->
                <div class="p-8 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-600 rounded-full p-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-green-600 font-semibold uppercase">Modificar Pedido</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $Pedidos->numero_pedido }}</p>
                        </div>
                    </div>
                </div>

                <!-- Mensagem de erro geral -->
                @if ($errors->any())
                    <div class="m-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                        <p class="font-semibold mb-2">Ocorreram erros ao salvar:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulário de Edição -->
                <form action="{{ route('pedidos.update', $Pedidos->id) }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Seção de Identificação -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Identificação do Pedido
                        </h3>

                        <div class="space-y-4">
                            <!-- Número do Pedido -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Número do Pedido
                                </label>
                                <input
                                    type="text"
                                    name="numero_pedido"
                                    value="{{ old('numero_pedido', $Pedidos->numero_pedido) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                    required
                                >
                                @error('numero_pedido') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Valores e Status -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Valores e Status
                        </h3>

                        <div class="space-y-4">
                            <!-- Valor Total -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Valor Total (R$)
                                </label>
                                <input
                                    type="number"
                                    name="valor_total"
                                    value="{{ old('valor_total', $Pedidos->valor_total) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                    step="0.01"
                                    min="0"
                                    required
                                >
                                @error('valor_total') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status e Método de Pagamento em linha -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 mb-2">
                                        <svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 5a2 2 0 110 4 2 2 0 010-4zM0 16.68a6 6 0 0112 0M19 14h-5M19 10h-5M19 18h-5"></path>
                                        </svg>
                                        Status
                                    </label>
                                    <select
                                        name="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                        required
                                    >
                                        <option value="">Selecione o status</option>
                                        <option value="Pendente" {{ old('status', $Pedidos->status) == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="Em Processamento" {{ old('status', $Pedidos->status) == 'Em Processamento' ? 'selected' : '' }}>Em Processamento</option>
                                        <option value="Enviado" {{ old('status', $Pedidos->status) == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                                        <option value="Entregue" {{ old('status', $Pedidos->status) == 'Entregue' ? 'selected' : '' }}>Entregue</option>
                                        <option value="Cancelado" {{ old('status', $Pedidos->status) == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700 mb-2">
                                        <svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h.01"></path>
                                        </svg>
                                        Método de Pagamento
                                    </label>
                                    <select
                                        name="metodo_pagamento"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                    >
                                        <option value="">Selecione o método</option>
                                        <option value="Dinheiro" {{ old('metodo_pagamento', $Pedidos->metodo_pagamento) == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                        <option value="Cartão de Crédito" {{ old('metodo_pagamento', $Pedidos->metodo_pagamento) == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                        <option value="Cartão de Débito" {{ old('metodo_pagamento', $Pedidos->metodo_pagamento) == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                        <option value="PIX" {{ old('metodo_pagamento', $Pedidos->metodo_pagamento) == 'PIX' ? 'selected' : '' }}>PIX</option>
                                        <option value="Boleto" {{ old('metodo_pagamento', $Pedidos->metodo_pagamento) == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                                    </select>
                                    @error('metodo_pagamento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Observações -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Observações
                        </h3>

                        <div class="space-y-4">
                            <!-- Observações -->
                            <div>
                                <label class="block font-medium text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    Observações do Pedido
                                </label>
                                <textarea
                                    name="observacoes"
                                    rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition resize-none"
                                    placeholder="Observações adicionais sobre o pedido">{{ old('observacoes', $Pedidos->observacoes) }}</textarea>
                                @error('observacoes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                        <a
                            href="{{ route('pedidos.index') }}"
                            class="px-6 py-2 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200"
                        >
                            Cancelar
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>