<?php

namespace App\Filament\Resources\Tickets\Tables;

use App\Models\Ticket;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceGroup.number')
                    ->label('Grupo')
                    ->sortable(),
                TextColumn::make('family.name')
                    ->label('Família')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brother.name')
                    ->label('Irmão')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pdf_filename')
                    ->label('PDF')
                    ->searchable()
                    ->limit(42),
                TextColumn::make('internal_code')
                    ->label('Código')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Ticket::STATUS_LABELS[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_group_id')
                    ->label('Grupo')
                    ->relationship('serviceGroup', 'name'),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Ticket::STATUS_LABELS),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('PDF')
                    ->url(fn (Ticket $record) => $record->pdf_path ? route('tickets.download', $record->public_token) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Ticket $record) => (bool) $record->pdf_path),
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->url(fn (Ticket $record) => 'https://wa.me/?text='.rawurlencode($record->whatsappText()))
                    ->openUrlInNewTab()
                    ->visible(fn (Ticket $record) => (bool) $record->pdf_path && (bool) $record->brother),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
