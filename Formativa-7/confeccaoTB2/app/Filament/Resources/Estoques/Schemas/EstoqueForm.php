<?php

namespace App\Filament\Resources\Estoques\Schemas;

use App\Models\fornecedor;
use App\Models\produto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EstoqueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('produto_id')
                    ->label('Produto')
                    ->options(fn() => produto::whereDoesntHave('estoque')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('fornecedor_id')
                    ->label('Fornecedor')
                    ->options(fornecedor::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                TextInput::make('quantidade')
                    ->label('Quantidade')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('quantidade_minima')
                    ->label('Quantidade Mínima')
                    ->numeric()
                    ->default(10)
                    ->required(),
                TextInput::make('localizacao')
                    ->label('Localização')
                    ->nullable(),
                TextInput::make('lote')
                    ->label('Lote')
                    ->nullable(),
                DatePicker::make('data_validade')
                    ->label('Data de Validade')
                    ->nullable(),
            ]);
    }
}
