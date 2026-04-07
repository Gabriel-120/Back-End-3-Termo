<?php

namespace App\Filament\Resources\Fornecedors\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FornecedorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações Empresariais')
                    ->description('Dados da empresa fornecedora')
                    ->icon('heroicon-o-building-office-2')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Razão Social')
                            ->placeholder('Empresa LTDA')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('documento')
                            ->label('CNPJ')
                            ->placeholder('12.345.678/0001-90')
                            ->mask('99.999.999/9999-99')
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
                            ->placeholder('contato@fornecedor.com'),
                        TextInput::make('telefone')
                            ->label('Telefone')
                            ->mask('(99) 9999-9999')
                            ->placeholder('(11) 3333-3333'),
                        TextInput::make('celular')
                            ->label('Celular')
                            ->mask('(99) 99999-9999')
                            ->placeholder('(11) 99999-9999')
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
