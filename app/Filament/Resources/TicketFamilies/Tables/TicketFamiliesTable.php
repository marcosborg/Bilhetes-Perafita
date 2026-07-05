<?php

namespace App\Filament\Resources\TicketFamilies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketFamiliesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceGroup.number')
                    ->label('Grupo')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Família')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brothers_count')
                    ->label('Irmãos')
                    ->counts('brothers'),
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
