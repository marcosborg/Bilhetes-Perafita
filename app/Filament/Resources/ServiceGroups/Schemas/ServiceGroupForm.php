<?php

namespace App\Filament\Resources\ServiceGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Número')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(7),
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('responsible_name')
                    ->label('Responsável')
                    ->maxLength(255),
                TextInput::make('responsible_phone')
                    ->label('Telefone')
                    ->maxLength(255),
                TextInput::make('public_token')
                    ->label('Token público')
                    ->maxLength(80),
            ]);
    }
}
