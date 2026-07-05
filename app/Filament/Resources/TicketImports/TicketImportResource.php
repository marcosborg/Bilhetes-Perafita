<?php

namespace App\Filament\Resources\TicketImports;

use App\Filament\Resources\TicketImports\Pages\CreateTicketImport;
use App\Filament\Resources\TicketImports\Pages\EditTicketImport;
use App\Filament\Resources\TicketImports\Pages\ListTicketImports;
use App\Filament\Resources\TicketImports\Schemas\TicketImportForm;
use App\Filament\Resources\TicketImports\Tables\TicketImportsTable;
use App\Models\TicketImport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketImportResource extends Resource
{
    protected static ?string $model = TicketImport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'importação';

    protected static ?string $pluralModelLabel = 'importações';

    public static function form(Schema $schema): Schema
    {
        return TicketImportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketImportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTicketImports::route('/'),
            'create' => CreateTicketImport::route('/create'),
            'edit' => EditTicketImport::route('/{record}/edit'),
        ];
    }
}
