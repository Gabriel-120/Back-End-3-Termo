<?php

namespace App\Filament\Resources\Insumos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InsumoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Nome do Insumo')
                    ->copyable()
                    ->columnSpanFull(),
                TextEntry::make('unidade_medida')
                    ->label('Unidade de Medida')
                    ->placeholder('-'),
                TextEntry::make('preco_custo')
                    ->label('Preço de Custo')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 2, ',', '.') : '-'),
                TextEntry::make('estoque')
                    ->label('Quantidade em Estoque')
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
