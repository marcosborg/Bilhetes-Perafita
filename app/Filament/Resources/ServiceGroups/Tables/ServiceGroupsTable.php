<?php

namespace App\Filament\Resources\ServiceGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Grupo')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('responsible_name')
                    ->label('Responsável')
                    ->searchable(),
                TextColumn::make('families_count')
                    ->label('Famílias')
                    ->counts('families'),
                TextColumn::make('tickets_count')
                    ->label('Bilhetes')
                    ->counts('tickets'),
                TextColumn::make('public_token')
                    ->label('Link')
                    ->formatStateUsing(fn ($record) => route('groups.portal', $record))
                    ->copyable()
                    ->limit(36),
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
