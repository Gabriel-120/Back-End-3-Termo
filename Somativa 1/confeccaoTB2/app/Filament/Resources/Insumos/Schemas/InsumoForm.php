<?php

namespace App\Filament\Resources\Insumos\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InsumoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Insumo')
                    ->description('Dados básicos do insumo')
                    ->icon('heroicon-o-puzzle-piece')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome do Insumo')
                            ->placeholder('Linha de Costura')
                            ->required()
                            ->columnSpan(1),
                        Select::make('unidade_medida')
                            ->label('Unidade de Medida')
                            ->placeholder('Selecione uma unidade')
                            ->options([
                                'kg' => 'Quilograma (kg)',
                                'g' => 'Grama (g)',
                                'l' => 'Litro (l)',
                                'ml' => 'Mililitro (ml)',
                                'm' => 'Metro (m)',
                                'cm' => 'Centímetro (cm)',
                                'un' => 'Unidade (un)',
                                'metros' => 'Metros',
                                'rolos' => 'Rolos',
                            ])
                            ->required()
                            ->columnSpan(1),
                    ]),
                Section::make('Dados Comerciais')
                    ->description('Preço e estoque')
                    ->icon('heroicon-o-calculator')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('preco_custo')
                            ->label('Preço de Custo')
                            ->placeholder('0.00')
                            ->numeric()
                            ->prefix('R$')
                            ->step('0.01')
                            ->minValue(0),
                        TextInput::make('estoque')
                            ->label('Quantidade em Estoque')
                            ->placeholder('0.00')
                            ->numeric()
                            ->step('0.01')
                            ->minValue(0)
                            ->default(0),
                    ]),
            ]);
    }
}
