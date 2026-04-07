<?php

namespace App\Filament\Resources\Estoques\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EstoqueInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('produto.name')
                    ->label('Produto')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('fornecedor.name')
                    ->label('Fornecedor')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('quantidade')
                    ->label('Quantidade')
                    ->placeholder('-'),
                TextEntry::make('quantidade_minima')
                    ->label('Quantidade Mínima')
                    ->placeholder('-'),
                TextEntry::make('localizacao')
                    ->label('Localização')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('lote')
                    ->label('Lote')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('data_validade')
                    ->label('Data de Validade')
                    ->date('d/m/Y')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Data de Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
