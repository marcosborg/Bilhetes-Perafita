<?php

namespace App\Filament\Resources\TicketImports\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TicketImportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('excel_path')->label('Excel')->disabled(),
                TextInput::make('zip_path')->label('ZIP')->disabled(),
                TextInput::make('zip_pdf_count')->label('PDFs no ZIP')->disabled(),
                TextInput::make('mapped_ticket_count')->label('Mapeados')->disabled(),
                TextInput::make('missing_pdf_count')->label('PDFs em falta')->disabled(),
                TextInput::make('unmapped_pdf_count')->label('PDFs sem correspondência')->disabled(),
                Textarea::make('warnings')
                    ->label('Avisos')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }
}
