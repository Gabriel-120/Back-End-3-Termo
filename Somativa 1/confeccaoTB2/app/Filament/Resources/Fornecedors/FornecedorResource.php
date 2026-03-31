<?php

namespace App\Filament\Resources\Fornecedors;

use App\Filament\Resources\Fornecedors\Pages\CreateFornecedor;
use App\Filament\Resources\Fornecedors\Pages\EditFornecedor;
use App\Filament\Resources\Fornecedors\Pages\ListFornecedors;
use App\Filament\Resources\Fornecedors\Pages\ViewFornecedor;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Filament\Resources\Fornecedors\Schemas\FornecedorForm;
use App\Filament\Resources\Fornecedors\Schemas\FornecedorInfolist;
use App\Filament\Resources\Fornecedors\Tables\FornecedorsTable;
use App\Models\Fornecedor;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FornecedorResource extends Resource
{
    protected static ?string $model = Fornecedor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = "Fornecedor";

    protected static ?string $modelLabel = "Fornecedor";

    protected static ?string $pluralModelLabel = "Fornecedores";

    protected static ?string $recordTitleAttribute = 'fornecedor';

    public static function form(Schema $schema): Schema
    {
        // return FornecedorForm::configure($schema);
        return $schema->schema([
            TextInput::make('name')->required()->label('Nome Completo'),
            TextInput::make('email')->email()->label('E-mail'),
            TextInput::make('telefone')->tel()->label('Telefone/Fixo')->mask('(99) 99999-9999'),
            TextInput::make('celular')->tel()->label('Celular/Zap')->mask('(99) 99999-9999'),
            TextInput::make('documento')->label('CPF ou CNPJ')->mask(RawJs::make
            (<<<'JS'
            $input.lenght > 14 ? '99.999.999/9999-99' : '999.999.999-99'
        JS)),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FornecedorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return FornecedorsTable::configure($table);
        return $table->columns([
            TextColumn::make('name')->searchable()->label('Nome'),
            TextColumn::make('email')->searchable()->label('Email'),
            TextColumn::make('telefone')->label('Telefone')->formatStateUsing(function (?string $state): string {
                if (!$state) {
                    return '';
                }

                $digits = preg_replace('/\D/', '', $state);

                if (strlen($digits) === 10) {
                    return '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 4) . '-' . substr($digits, 6);
                }

                if (strlen($digits) === 11) {
                    return '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 5) . '-' . substr($digits, 7);
                }

                return $state;
            }),
            TextColumn::make('celular')->label('Celular')->formatStateUsing(function (?string $state): string {
                if (!$state) {
                    return '';
                }

                $digits = preg_replace('/\D/', '', $state);

                if (strlen($digits) === 10) {
                    return '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 4) . '-' . substr($digits, 6);
                }

                if (strlen($digits) === 11) {
                    return '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 5) . '-' . substr($digits, 7);
                }

                return $state;
            }),
            TextColumn::make('documento')->label('Documento')->formatStateUsing(function (string $state): string {
                if (!$state) return '';
                $clean = preg_replace('/\D/', '', $state);
                if (strlen($clean) == 11) {
                    // CPF
                    return substr($clean, 0, 3) . '.' . substr($clean, 3, 3) . '.' . substr($clean, 6, 3) . '-' . substr($clean, 9);
                } elseif (strlen($clean) == 14) {
                    // CNPJ
                    return substr($clean, 0, 2) . '.' . substr($clean, 2, 3) . '.' . substr($clean, 5, 3) . '/' . substr($clean, 8, 4) . '-' . substr($clean, 12);
                }
                return $state;
            }),
        ])
        ->recordActions([
            ViewAction::make()->modal(),
            EditAction::make()->modal(),
        ])
        ->toolbarActions([
            CreateAction::make()->modal(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFornecedors::route('/'),
            'create' => CreateFornecedor::route('/create'),
            'view' => ViewFornecedor::route('/{record}'),
            'edit' => EditFornecedor::route('/{record}/edit'),
        ];
    }
}
