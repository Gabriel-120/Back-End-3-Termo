<?php

namespace App\Filament\Resources\Role2s\Pages;

use App\Filament\Resources\Role2s\Role2Resource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRole2s extends ListRecords
{
    protected static string $resource = Role2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
