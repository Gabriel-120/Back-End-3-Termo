<?php

namespace App\Filament\Resources\Role2s\Pages;

use App\Filament\Resources\Role2s\Role2Resource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole2 extends ViewRecord
{
    protected static string $resource = Role2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
