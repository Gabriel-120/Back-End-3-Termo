<?php

namespace App\Filament\Resources\Role2s\Pages;

use App\Filament\Resources\Role2s\Role2Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRole2 extends EditRecord
{
    protected static string $resource = Role2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
