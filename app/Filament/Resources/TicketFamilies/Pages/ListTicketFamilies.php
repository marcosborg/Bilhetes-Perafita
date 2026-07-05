<?php

namespace App\Filament\Resources\TicketFamilies\Pages;

use App\Filament\Resources\TicketFamilies\TicketFamilyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTicketFamilies extends ListRecords
{
    protected static string $resource = TicketFamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
