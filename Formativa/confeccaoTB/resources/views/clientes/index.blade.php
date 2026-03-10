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
                        <table class="w-full">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Nome</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">CPF</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Telefone</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Endereço</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($Clientes as $clientes)
                                    <tr class="hover:bg-blue-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $clientes->nome }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $clientes->cpf }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $clientes->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $clientes->telefone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $clientes->endereco }}</td>
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