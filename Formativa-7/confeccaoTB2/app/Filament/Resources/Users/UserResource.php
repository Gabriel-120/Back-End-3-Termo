<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function canAccess(): bool {
        return auth()->user()?->can('Admin') ?? False;
        // auth()->user()?->hasRole('Estoque') ?? False
    } 

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Painel de Usuários';
    protected static ?string $modelLabel = 'Criar Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static string|UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $recordTitleAttribute = 'usuarios';

    public static function form(Schema $schema): Schema
    {
        // return UserForm::configure($schema);
        return $schema->schema([
            TextInput::make('name')
                ->label('Nome')
                ->required(),

            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required(),

            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->required(fn(string $operation): bool => $operation === 'create')
                ->dehydrated(fn(?string $state) => filled($state))
                ->hiddenOn('view'),

            Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->label('Cargo / Permissões'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return UsersTable::configure($table);
        return $table->columns([
            TextColumn::make('name')
                ->label('Nome')
                ->searchable()
                ->sortable(),

            TextColumn::make('email')
                ->label('E-mail')
                ->searchable(),

            TextColumn::make('roles.name')
                ->label('Cargos')
                ->separator(', ')
                ->searchable()
                ->sortable(),

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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
