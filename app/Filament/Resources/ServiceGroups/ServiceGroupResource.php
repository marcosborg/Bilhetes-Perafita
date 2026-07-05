<?php

namespace App\Filament\Resources\ServiceGroups;

use App\Filament\Resources\ServiceGroups\Pages\CreateServiceGroup;
use App\Filament\Resources\ServiceGroups\Pages\EditServiceGroup;
use App\Filament\Resources\ServiceGroups\Pages\ListServiceGroups;
use App\Filament\Resources\ServiceGroups\Schemas\ServiceGroupForm;
use App\Filament\Resources\ServiceGroups\Tables\ServiceGroupsTable;
use App\Models\ServiceGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ServiceGroupResource extends Resource
{
    protected static ?string $model = ServiceGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'grupo de serviço';

    protected static ?string $pluralModelLabel = 'grupos de serviço';

    public static function form(Schema $schema): Schema
    {
        return ServiceGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceGroupsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user?->shouldDefaultToServiceGroup()) {
            $query->whereKey($user->service_group_id);
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
            'index' => ListServiceGroups::route('/'),
            'create' => CreateServiceGroup::route('/create'),
            'edit' => EditServiceGroup::route('/{record}/edit'),
        ];
    }
}
