<?php

namespace App\Filament\Resources\Brothers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BrothersTable
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
                TextColumn::make('name')
                    ->label('Irmão')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('ticket.id')
                    ->label('PDF')
                    ->boolean()
                    ->state(fn ($record) => (bool) $record->ticket),
            ])
            ->filters([
                SelectFilter::make('service_group_id')
                    ->label('Grupo')
                    ->relationship('serviceGroup', 'name'),
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
