<?php

namespace App\Filament\Resources\TicketImports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketImportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('zip_pdf_count')
                    ->label('PDFs ZIP'),
                TextColumn::make('mapped_ticket_count')
                    ->label('Mapeados'),
                TextColumn::make('missing_pdf_count')
                    ->label('Em falta'),
                TextColumn::make('unmapped_pdf_count')
                    ->label('Sem correspondência'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
