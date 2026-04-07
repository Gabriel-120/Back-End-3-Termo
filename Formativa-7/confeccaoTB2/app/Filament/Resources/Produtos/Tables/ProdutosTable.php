<?php

namespace App\Filament\Resources\Produtos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class ProdutosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do Produto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('referencia')
                    ->label('Referência')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('preco_venda')
                    ->label('Preço')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 2, ',', '.') : '-'),
                Tables\Columns\TextColumn::make('estoque')
                    ->label('Estoque')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Visualizar')->modal(),
                EditAction::make()->label('Editar')->modal(),
            ])
            ->actions([
                ViewAction::make()->label('Visualizar')->modal(),
                EditAction::make()->label('Editar')->modal(),
                \Filament\Actions\DeleteAction::make()->label('Deletar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
