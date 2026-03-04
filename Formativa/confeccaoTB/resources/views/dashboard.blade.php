<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-semibold mb-8">Bem-vindo ao sistema de gerenciamento!</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Botão Clientes -->
                        <a href="{{ route('clientes.index') }}" class="block">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg shadow-lg p-6 transition duration-300 transform hover:scale-105 cursor-pointer h-full">
                                <svg class="w-12 h-12 text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM5 20h16a2 2 0 002-2v-2a6 6 0 00-6-6H5a6 6 0 000 12z"></path>
                                </svg>
                                <h4 class="text-xl font-bold text-white">Clientes</h4>
                                <p class="text-blue-100 text-sm mt-2">Gerenciar clientes</p>
                            </div>
                        </a>

                        <!-- Botão Pedidos -->
                        <a href="{{ route('pedidos.index') }}" class="block">
                            <div class="bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg shadow-lg p-6 transition duration-300 transform hover:scale-105 cursor-pointer h-full">
                                <svg class="w-12 h-12 text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <h4 class="text-xl font-bold text-white">Pedidos</h4>
                                <p class="text-green-100 text-sm mt-2">Gerenciar pedidos</p>
                            </div>
                        </a>

                        <!-- Botão Produtos -->
                        <a href="{{ route('produtos.index') }}" class="block">
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-lg shadow-lg p-6 transition duration-300 transform hover:scale-105 cursor-pointer h-full">
                                <svg class="w-12 h-12 text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                                </svg>
                                <h4 class="text-xl font-bold text-white">Produtos</h4>
                                <p class="text-purple-100 text-sm mt-2">Gerenciar produtos</p>
                            </div>
                        </a>

                        <!-- Botão Fornecedores -->
                        <a href="{{ route('fornecedores.index') }}" class="block">
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-lg shadow-lg p-6 transition duration-300 transform hover:scale-105 cursor-pointer h-full">
                                <svg class="w-12 h-12 text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h4 class="text-xl font-bold text-white">Fornecedores</h4>
                                <p class="text-orange-100 text-sm mt-2">Gerenciar fornecedores</p>
                            </div>
                        </a>

                        <!-- Botão Estoque -->
                        <a href="{{ route('estoque.index') }}" class="block">
                            <div class="bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-lg shadow-lg p-6 transition duration-300 transform hover:scale-105 cursor-pointer h-full">
                                <svg class="w-12 h-12 text-white mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                <h4 class="text-xl font-bold text-white">Estoque</h4>
                                <p class="text-red-100 text-sm mt-2">Gerenciar estoque</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
