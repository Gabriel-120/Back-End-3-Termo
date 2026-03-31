<?php

namespace App\Filament\Resources\Estoques\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class EstoquesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produto.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fornecedor.name')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantidade')
                    ->label('Quantidade')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantidade_minima')
                    ->label('Qtd. Mínima')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('localizacao')
                    ->label('Localização')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lote')
                    ->label('Lote')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_validade')
                    ->label('Data Validade')
                    ->date('d/m/Y')
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
