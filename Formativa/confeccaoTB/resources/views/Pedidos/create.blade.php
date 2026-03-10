<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Novo Pedido') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg">

                <!-- Formulário apontando para a rota de salvar -->
                <form action="{{ route('pedidos.store') }}" method="POST" class="space-y-4">
                    @csrf <!-- Obrigatório para segurança no Laravel -->

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Número do Pedido</label>
                        <input type="text" name="numero_pedido" value="{{ old('numero_pedido') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                        @error('numero_pedido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Valor Total</label>
                            <input type="number" step="0.01" name="valor_total" value="{{ old('valor_total') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                            @error('valor_total') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Status</label>
                            <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                                <option value="">Selecione</option>
                                <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="processando" {{ old('status') == 'processando' ? 'selected' : '' }}>Processando</option>
                                <option value="concluido" {{ old('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                <option value="cancelado" {{ old('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Método de Pagamento</label>
                        <select name="metodo_pagamento" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                            <option value="">Selecione</option>
                            <option value="dinheiro" {{ old('metodo_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="cartao" {{ old('metodo_pagamento') == 'cartao' ? 'selected' : '' }}>Cartão</option>
                            <option value="pix" {{ old('metodo_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="boleto" {{ old('metodo_pagamento') == 'boleto' ? 'selected' : '' }}>Boleto</option>
                        </select>
                        @error('metodo_pagamento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Observações</label>
                        <textarea name="observacoes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" rows="3">{{ old('observacoes') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('pedidos.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Salvar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>