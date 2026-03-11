<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nossa confeccao</h2>
        <a href="{{ route('clientes.create')}}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150"> + Novo Cliente</a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Mensagem de Sucesso no logo -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border-1-4 border-green-500 text-green-700 shadow-sm rounded-r">
                {{ session('success')}}
            </div>
        @endif
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM5 20h16a2 2 0 002-2v-2a6 6 0 00-6-6H5a6 6 0 000 12z"></path>
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900">Lista de Clientes</h3>
                        </div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
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
                            <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">
                                Total: {{ isset($Clientes) ? $Clientes->count() : 0 }} cliente(s)
                            </div>
                        </div>
                        @if(!isset($Clientes) || $Clientes->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-600 text-lg">Nenhum cliente encontrado.</p>
                            </div>
                        @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($Clientes as $clientes)
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md hover:shadow-lg transition duration-300 p-6 border-l-4 border-blue-600">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="bg-blue-600 rounded-full p-3">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-blue-600 font-semibold uppercase">Cliente</p>
                                                <p class="text-lg font-bold text-gray-900">{{ $clientes->nome }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3 text-gray-700">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-600 uppercase font-semibold">CPF</p>
                                                <p class="font-semibold">{{ $clientes->cpf }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 text-gray-700">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-600 uppercase font-semibold">Email</p>
                                                <p class="font-semibold text-sm truncate">{{ $clientes->email }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 text-gray-700">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 7.492a1 1 0 00.502.756l2.048 1.029a11.042 11.042 0 010 1.078l-2.048 1.029a1 1 0 00-.502.756l-1.498 7.492a1 1 0 00-.948.684H5a2 2 0 01-2-2V5z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-600 uppercase font-semibold">Telefone</p>
                                                <p class="font-semibold">{{ $clientes->telefone }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start gap-3 text-gray-700">
                                            <svg class="w-5 h-5 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-600 uppercase font-semibold">Endereço</p>
                                                <p class="font-semibold text-sm">{{ $clientes->endereco }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Footer com Botões de Ação -->
                                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-blue-200">
                                        <a href="{{ route('clientes.edit', $clientes->id) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                        </a>
                                        <form action="{{ route('clientes.destroy', $clientes->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este cliente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Deletar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>