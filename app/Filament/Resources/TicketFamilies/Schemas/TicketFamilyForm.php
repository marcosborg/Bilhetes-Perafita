<?php

namespace App\Filament\Resources\TicketFamilies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketFamilyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_group_id')
                    ->label('Grupo')
                    ->relationship('serviceGroup', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Família')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
