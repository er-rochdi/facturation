<?php

namespace App\Filament\Resources\Deponses;

use App\Filament\Resources\Deponses\Pages\CreateDeponse;
use App\Filament\Resources\Deponses\Pages\EditDeponse;
use App\Filament\Resources\Deponses\Pages\ListDeponses;
use App\Filament\Resources\Deponses\Schemas\DeponseForm;
use App\Filament\Resources\Deponses\Tables\DeponsesTable;
use App\Models\Deponse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DeponseResource extends Resource
{
    protected static ?string $model = Deponse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Dépenses';

    protected static ?string $modelLabel = 'Dépense';

    protected static ?string $pluralModelLabel = 'Dépenses';

    protected static UnitEnum|string|null $navigationGroup = 'Finances';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DeponseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeponsesTable::configure($table);
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
            'index' => ListDeponses::route('/'),
            'create' => CreateDeponse::route('/create'),
            'edit' => EditDeponse::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
