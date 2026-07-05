<?php

namespace App\Filament\Resources\TicketFamilies;

use App\Filament\Resources\TicketFamilies\Pages\CreateTicketFamily;
use App\Filament\Resources\TicketFamilies\Pages\EditTicketFamily;
use App\Filament\Resources\TicketFamilies\Pages\ListTicketFamilies;
use App\Filament\Resources\TicketFamilies\Schemas\TicketFamilyForm;
use App\Filament\Resources\TicketFamilies\Tables\TicketFamiliesTable;
use App\Models\TicketFamily;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketFamilyResource extends Resource
{
    protected static ?string $model = TicketFamily::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'família';

    protected static ?string $pluralModelLabel = 'famílias';

    public static function form(Schema $schema): Schema
    {
        return TicketFamilyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketFamiliesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user?->shouldDefaultToServiceGroup()) {
            $query->where('service_group_id', $user->service_group_id);
        }

        return $query;
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
            'index' => ListTicketFamilies::route('/'),
            'create' => CreateTicketFamily::route('/create'),
            'edit' => EditTicketFamily::route('/{record}/edit'),
        ];
    }
}
