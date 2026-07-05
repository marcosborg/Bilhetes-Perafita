<?php

namespace App\Filament\Resources\Brothers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrotherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_group_id')
                    ->label('Grupo')
                    ->relationship('serviceGroup', 'name')
                    ->required(),
                Select::make('ticket_family_id')
                    ->label('Família')
                    ->relationship('family', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_under_12')->label('<12'),
                Toggle::make('is_over_75')->label('>75'),
                Toggle::make('has_locomotion_need')->label('Locom.'),
                Toggle::make('has_mobility_need')->label('Mobil.'),
                Toggle::make('normal_ticket')->label('Normal'),
                Toggle::make('andante')->label('Andante'),
                Toggle::make('distico')->label('Dístico'),
            ]);
    }
}
