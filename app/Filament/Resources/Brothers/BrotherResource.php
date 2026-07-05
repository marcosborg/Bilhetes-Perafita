<?php

namespace App\Filament\Resources\Brothers;

use App\Filament\Resources\Brothers\Pages\CreateBrother;
use App\Filament\Resources\Brothers\Pages\EditBrother;
use App\Filament\Resources\Brothers\Pages\ListBrothers;
use App\Filament\Resources\Brothers\Schemas\BrotherForm;
use App\Filament\Resources\Brothers\Tables\BrothersTable;
use App\Models\Brother;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BrotherResource extends Resource
{
    protected static ?string $model = Brother::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'irmão';

    protected static ?string $pluralModelLabel = 'irmãos';

    public static function form(Schema $schema): Schema
    {
        return BrotherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrothersTable::configure($table);
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
            'index' => ListBrothers::route('/'),
            'create' => CreateBrother::route('/create'),
            'edit' => EditBrother::route('/{record}/edit'),
        ];
    }
}
