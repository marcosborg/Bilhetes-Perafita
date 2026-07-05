<?php

namespace App\Filament\Resources\Brothers\Pages;

use App\Filament\Resources\Brothers\BrotherResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBrother extends EditRecord
{
    protected static string $resource = BrotherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
