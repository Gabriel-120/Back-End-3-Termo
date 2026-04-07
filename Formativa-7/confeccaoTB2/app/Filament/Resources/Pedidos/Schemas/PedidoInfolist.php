<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PedidoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('cliente_id')
                    ->label('ID do Cliente')
                    ->numeric(),
                TextEntry::make('status')
                    ->label('Status'),
                TextEntry::make('valor_total')
                    ->label('Valor Total')
                    ->numeric()
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
