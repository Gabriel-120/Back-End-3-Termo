<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações Pessoais')
                    ->description('Dados básicos do cliente')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->placeholder('João Silva Santos')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('documento')
                            ->label('CPF/CNPJ')
                            ->placeholder('123.456.789-00')
                            ->mask(fn ($state) => strlen(str_replace(['.', '-', '/'], '', $state)) === 14 ? '99.999.999/9999-99' : '999.999.999-99')
                            ->unique(ignoreRecord: true),
                    ]),
                Section::make('Contato')
                    ->description('Informações de contato')
                    ->icon('heroicon-o-phone')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('cliente@example.com'),
                        TextInput::make('telefone')
                            ->label('Telefone')
                            ->mask('(99) 9999-9999')
                            ->placeholder('(11) 3333-3333'),
                    ]),
            ]);
    }
}
