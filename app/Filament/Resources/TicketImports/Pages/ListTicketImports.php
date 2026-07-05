<?php

namespace App\Filament\Resources\TicketImports\Pages;

use App\Filament\Resources\TicketImports\TicketImportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTicketImports extends ListRecords
{
    protected static string $resource = TicketImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
