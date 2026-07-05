<?php

namespace App\Filament\Resources\Tickets\Schemas;

use App\Models\Ticket;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_group_id')
                    ->label('Grupo')
                    ->relationship('serviceGroup', 'name'),
                Select::make('ticket_family_id')
                    ->label('Família')
                    ->relationship('family', 'name'),
                Select::make('brother_id')
                    ->label('Irmão')
                    ->relationship('brother', 'name')
                    ->searchable(),
                TextInput::make('pdf_filename')
                    ->label('Ficheiro PDF')
                    ->required()
                    ->maxLength(255),
                TextInput::make('pdf_path')
                    ->label('Caminho privado')
                    ->maxLength(255),
                TextInput::make('internal_code')
                    ->label('Código interno')
                    ->maxLength(255),
                Select::make('status')
                    ->label('Estado')
                    ->options(Ticket::STATUS_LABELS)
                    ->required(),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
