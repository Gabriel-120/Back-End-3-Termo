<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Nome Completo')
                    ->copyable()
                    ->columnSpanFull(),
                TextEntry::make('documento')
                    ->label('CPF/CNPJ')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('email')
                    ->label('Email')
                    ->placeholder('-')
                    ->copyable(),
                TextEntry::make('telefone')
                    ->label('Telefone')
                    ->placeholder('-')
                    ->copyable(),
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
