<?php

namespace App\Filament\Resources\Clientes;

use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\EditCliente;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Filament\Resources\Clientes\Pages\ViewCliente;
use App\Filament\Resources\Clientes\Schemas\ClienteForm;
use App\Filament\Resources\Clientes\Schemas\ClienteInfolist;
use App\Filament\Resources\Clientes\Tables\ClientesTable;
use App\Models\Cliente;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use PhpParser\Node\Stmt\Label;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool {
        return (auth()->user()?->hasRole('Admin') ?? False) || (auth()->user()?->hasRole('Gerente') ?? False);
    }

    protected static ?string $navigationLabel = 'Painel de Clientes';
    protected static ?string $modelLabel = 'Cadastrar Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static string|UnitEnum|null $navigationGroup = 'Cadastro Geral';

    protected static ?string $recordTitleAttribute = 'cliente';

    public static function form(Schema $schema): Schema
    {
        // return ClienteForm::configure($schema);

        return $schema->schema([
            TextInput::make('name')->required()->label('Nome Completo'),
            TextInput::make('email')->email()->label('E-mail'),
            TextInput::make('telefone')->tel()->label('Telefone/Zap')->mask('(99) 99999-9999'),
            TextInput::make('documento')->label('CPF ou CNPJ')->mask(RawJs::make
            (<<<'JS'
            $input.lenght > 14 ? '99.999.999/9999-99' : '999.999.999-99'
        JS)),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClienteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return ClientesTable::configure($table);
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('telefone'),
            TextColumn::make('documento'),
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
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'view' => ViewCliente::route('/{record}'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
}
