<?php

namespace App\Filament\Resources\Produtos\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProdutoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Produto')
                    ->description('Dados básicos do produto')
                    ->icon('heroicon-o-cube')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do Produto')
                            ->placeholder('Camiseta Básica')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('referencia')
                            ->label('Referência/SKU')
                            ->placeholder('REF-001')
                            ->columnSpan(1),
                    ]),
                Section::make('Dados Comerciais')
                    ->description('Preço e estoque')
                    ->icon('heroicon-o-calculator')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('preco_venda')
                            ->label('Preço de Venda')
                            ->placeholder('0.00')
                            ->numeric()
                            ->prefix('R$')
                            ->step('0.01')
                            ->minValue(0),
                        TextInput::make('estoque')
                            ->label('Estoque')
                            ->placeholder('0')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),
            ]);
    }
}
