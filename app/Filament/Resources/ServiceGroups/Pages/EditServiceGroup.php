<?php

namespace App\Filament\Resources\ServiceGroups\Pages;

use App\Filament\Resources\ServiceGroups\ServiceGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceGroup extends EditRecord
{
    protected static string $resource = ServiceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
