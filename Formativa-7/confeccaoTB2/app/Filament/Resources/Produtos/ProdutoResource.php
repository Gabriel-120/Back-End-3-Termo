<?php

namespace App\Filament\Resources\Produtos;

use App\Filament\Resources\Produtos\Pages\CreateProduto;
use App\Filament\Resources\Produtos\Pages\EditProduto;
use App\Filament\Resources\Produtos\Pages\ListProdutos;
use App\Filament\Resources\Produtos\Pages\ViewProduto;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Filament\Resources\Produtos\Schemas\ProdutoForm;
use App\Filament\Resources\Produtos\Schemas\ProdutoInfolist;
use App\Filament\Resources\Produtos\Tables\ProdutosTable;
use App\Models\Produto;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProdutoResource extends Resource
{
    protected static ?string $model = Produto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool {
        return (auth()->user()?->hasRole('Admin') ?? False) || (auth()->user()?->hasRole('Estoque') ?? False);
    }

    protected static ?string $navigationLabel = 'Painel de Produtos';
    protected static ?string $modelLabel = 'Criar Produto';
    protected static ?string $pluralModelLabel = 'Produtos';
    protected static string|UnitEnum|null $navigationGroup = 'Estoque';

    protected static ?string $recordTitleAttribute = 'produto';

    public static function form(Schema $schema): Schema
    {
        // return ProdutoForm::configure($schema);
        return $schema->schema([
            Section::make('Informações do Produto')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->label('Nome do Produto'),

                    TextInput::make('referencia')
                        ->required()
                        ->label('Referência'),

                    TextInput::make('preco_venda')
                        ->numeric()
                        ->prefix('R$')
                        ->label('Preço de Venda'),
                ])->columns(3),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProdutoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return ProdutosTable::configure($table);
        return $table->columns([
            TextColumn::make('name')
                ->searchable()
                ->label('Nome'),

            TextColumn::make('referencia')
                ->label('Referência'),

            TextColumn::make('preco_venda')
                ->money('BRL')
                ->label('Preço de Venda'),
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
            'index' => ListProdutos::route('/'),
            'create' => CreateProduto::route('/create'),
            'view' => ViewProduto::route('/{record}'),
            'edit' => EditProduto::route('/{record}/edit'),
        ];
    }
}
