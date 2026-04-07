<?php

namespace App\Filament\Resources\Produtos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProdutoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Nome do Produto')
                    ->copyable()
                    ->columnSpanFull(),
                TextEntry::make('referencia')
                    ->label('Referência/SKU')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('preco_venda')
                    ->label('Preço de Venda')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 2, ',', '.') : '-'),
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
