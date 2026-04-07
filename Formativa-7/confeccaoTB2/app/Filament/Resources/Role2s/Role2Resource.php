<?php

namespace App\Filament\Resources\Role2s;

use App\Filament\Resources\Role2s\Pages\CreateRole2;
use App\Filament\Resources\Role2s\Pages\EditRole2;
use App\Filament\Resources\Role2s\Pages\ListRole2s;
use App\Filament\Resources\Role2s\Pages\ViewRole2;
use App\Filament\Resources\Role2s\Schemas\Role2Form;
use App\Filament\Resources\Role2s\Schemas\Role2Infolist;
use App\Filament\Resources\Role2s\Tables\Role2sTable;
// use App\Models\Role2;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class Role2Resource extends Resource
{
    protected static ?string $model = Role::class;
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    protected static ?string $navigationLabel = 'Painel de Cargos';
    protected static ?string $modelLabel = 'Criar Cargo';
    protected static ?string $pluralModelLabel = 'Cargos';
    protected static string|UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $recordTitleAttribute = 'Cargo';

    public static function form(Schema $schema): Schema
    {
        // return Role2Form::configure($schema);
        return $schema->schema([
            TextInput::make('name')
                ->label('Cargo')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Select::make('permissions')
                ->label('Permissão de Acesso')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return Role2Infolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return Role2sTable::configure($table);
        return $table->columns([
            TextColumn::make('name')
                ->label('Cargo')
                ->searchable()
                ->sortable(),

            TextColumn::make('guard_name')
                ->label('Guarda')
                ->searchable(),

            TextColumn::make('permissions_count')
                ->label('Permissões')
                ->counts('permissions'),

            TextColumn::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y')
                ->sortable(),
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
            'index' => ListRole2s::route('/'),
            'create' => CreateRole2::route('/create'),
            'view' => ViewRole2::route('/{record}'),
            'edit' => EditRole2::route('/{record}/edit'),
        ];
    }
}
