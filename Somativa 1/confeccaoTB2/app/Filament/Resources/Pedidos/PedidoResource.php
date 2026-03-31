<?php

namespace App\Filament\Resources\Pedidos;

use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\Pages\ViewPedido;
use App\Filament\Resources\Pedidos\Schemas\PedidoForm;
use App\Filament\Resources\Pedidos\Schemas\PedidoInfolist;
use App\Filament\Resources\Pedidos\Tables\PedidosTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pedido';

    public static function form(Schema $schema): Schema
    {
        // return PedidoForm::configure($schema);
        return $schema->schema([
            Select::make('cliente_id')
                ->relationship('cliente', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Selecione o cliente'),

            Select::make('status')
                ->options([
                    'Pendente' => 'Pendente',
                    'Em Produção' => 'Em Produção',
                    'Finalizado' => 'Finalizado',
                ])
                ->default('Pendente')
                ->required(),

            TextInput::make('valor_total')
                ->numeric()
                ->readOnly()
                ->prefix('R$'),

            Repeater::make('itens')
            ->relationship('itens')
            ->schema([
                Select::make('produto_id')
                    ->relationship('produto', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Produto')
                    ->columnSpan(2)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $produto = \App\Models\produto::find($state);
                            if ($produto && $produto->preco_venda) {
                                $set('preco_unitario', $produto->preco_venda);
                            }
                        }
                    }),

                TextInput::make('quantidade')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::CalcularTotal($get, $set))
                    ->columnSpan(2),

                TextInput::make('preco_unitario')
                    ->numeric()
                    ->prefix('R$')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::CalcularTotal($get, $set))
                    ->columnSpan(2),
            ])
            ->columnSpan(4)
            ->columnSpanFull()
            ->label('Produto do Pedido')
            ->live()

            ->afterStateUpdated(fn (Get $get, Set $set) => self::CalcularTotal($get, $set)),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PedidoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return PedidosTable::configure($table);
        return $table
        ->columns([
            TextColumn::make('cliente.name')
                ->label('Cliente')
                ->searchable()
                ->sortable(),

            TextColumn::make('status')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'Pendente' => 'warning',
                'Em Produção' => 'info',
                'Finalizado' => 'Success',
                default => 'gray'
            }),

            TextColumn::make('valor_total')
                ->label('Valor Total')
                ->money('BRL')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Data Pedido')
                ->dateTime('d/m/Y H:i')
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
            'index' => ListPedidos::route('/'),
            'create' => CreatePedido::route('/create'),
            'view' => ViewPedido::route('/{record}'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }

    public static function CalcularTotal(Get $get, Set $set): void {
        $itens = $get('itens') ?? [];
        $total = 0;

        foreach ($itens as $item) {
            $quantidade = (float) ($item['quantidade'] ?? 0);
            $preco = (float) ($item['preco_unitario'] ?? 0);

            $total += $quantidade * $preco;
        }

        $set('valor_total', number_format($total, 2, '.', ''));
    }

}
