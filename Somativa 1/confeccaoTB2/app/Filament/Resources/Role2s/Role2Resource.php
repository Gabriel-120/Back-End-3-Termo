<?php

namespace App\Filament\Resources\Role2s;

use App\Filament\Resources\Role2s\Pages\CreateRole2;
use App\Filament\Resources\Role2s\Pages\EditRole2;
use App\Filament\Resources\Role2s\Pages\ListRole2s;
use App\Filament\Resources\Role2s\Pages\ViewRole2;
use App\Filament\Resources\Role2s\Schemas\Role2Form;
use App\Filament\Resources\Role2s\Schemas\Role2Infolist;
use App\Filament\Resources\Role2s\Tables\Role2sTable;
use App\Models\Role2;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class Role2Resource extends Resource
{
    protected static ?string $model = Role2::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Cargo';

    public static function form(Schema $schema): Schema
    {
        return Role2Form::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return Role2Infolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Role2sTable::configure($table);
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
