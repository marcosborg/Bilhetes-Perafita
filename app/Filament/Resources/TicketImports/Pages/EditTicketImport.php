<?php

namespace App\Filament\Resources\TicketImports\Pages;

use App\Filament\Resources\TicketImports\TicketImportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTicketImport extends EditRecord
{
    protected static string $resource = TicketImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
