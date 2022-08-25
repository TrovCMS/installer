<?php

namespace App\Filament\Resources\Trov;

use Filament\Forms;
use Filament\Tables;
use App\Models\Linkable;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Trov\LinkableResource\RelationManagers;
use App\Filament\Resources\Trov\LinkableResource\Pages\EditLinkable;
use App\Filament\Resources\Trov\LinkableResource\Pages\ListLinkables;
use App\Filament\Resources\Trov\LinkableResource\Pages\CreateLinkable;

class LinkableResource extends Resource
{
    protected static ?string $model = Linkable::class;

    protected static ?string $label = 'Internal Linking Set';

    protected static ?string $navigationGroup = 'Site';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static bool $shouldRegisterNavigation = false;

    public static function getFormSchema(): array
    {
        return [
            Select::make('section')
                ->options(config('trov.linkable_sections'))
                ->required()
                ->columnSpan('full'),
            Repeater::make('links')
                ->schema([
                    TextInput::make('text')->required()->columnSpan(['md' => 1]),
                    TextInput::make('url')->required()->url(true)->columnSpan(['md' => 3]),
                ])
                ->createItemButtonLabel('Add Link')
                ->columns(['md' => 4])
                ->columnSpan('full'),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('section'),
            TextColumn::make('link_count')->label('# of Links')->formatStateUsing(function ($record) {
                return count($record->links);
            }),
        ];
    }

    public static function getPages(): array
    {
        return [];
    }
}
