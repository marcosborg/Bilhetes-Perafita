<?php

namespace App\Filament\Resources\Brothers\Pages;

use App\Filament\Resources\Brothers\BrotherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBrothers extends ListRecords
{
    protected static string $resource = BrotherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
