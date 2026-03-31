<?php

namespace App\Filament\Resources\Insumos;

use App\Filament\Resources\Insumos\Pages\CreateInsumo;
use App\Filament\Resources\Insumos\Pages\EditInsumo;
use App\Filament\Resources\Insumos\Pages\ListInsumos;
use App\Filament\Resources\Insumos\Pages\ViewInsumo;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Filament\Resources\Insumos\Schemas\InsumoForm;
use App\Filament\Resources\Insumos\Schemas\InsumoInfolist;
use App\Filament\Resources\Insumos\Tables\InsumosTable;
use App\Models\Insumo;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class InsumoResource extends Resource
{
    protected static ?string $model = Insumo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'insumo';

    public static function form(Schema $schema): Schema
    {
        // return InsumoForm::configure($schema);
        return $schema->schema([
            TextInput::make('name')->required()->label('Nome do Material'),
            // TextInput::make('unidade_medida')->required()->label('Unidade (KG, Metros, Un...)'),
            Select::make('unidade_medida')->options([
            'kg' => 'KG',
            'l' => 'L',
            'v' => 'V',
            ]),
            TextInput::make('preco_custo')->numeric()->prefix('R$')->label('Preço de Custo'),
            TextInput::make('estoque')->numeric()->default(0)->label('Qauntidade em Estoque'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InsumoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return InsumosTable::configure($table);
        return $table->columns([
            TextColumn::make('name')->searchable()->label('Nome'),
            TextColumn::make('unidade_medida')->label('Unidade de Medida'),
            TextColumn::make('preco_custo')->money('BRL')->label('Preço de Custo'),
            TextColumn::make('estoque')->numeric(decimalPlaces: 2)->label('Estoque'),
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
            'index' => ListInsumos::route('/'),
            'create' => CreateInsumo::route('/create'),
            'view' => ViewInsumo::route('/{record}'),
            'edit' => EditInsumo::route('/{record}/edit'),
        ];
    }
}
