<?php

namespace App\Filament\Resources\TicketFamilies\Pages;

use App\Filament\Resources\TicketFamilies\TicketFamilyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTicketFamily extends EditRecord
{
    protected static string $resource = TicketFamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
