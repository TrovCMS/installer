<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RunwayResource\Pages\CreateRunway;
use App\Filament\Resources\RunwayResource\Pages\EditRunway;
use App\Filament\Resources\RunwayResource\Pages\ListRunways;
use App\Forms\Components\PageBuilder;
use App\Models\Runway;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use FilamentAddons\Enums\Status;
use FilamentAddons\Forms\Components\Timestamps;
use FilamentAddons\Forms\Components\TitleWithSlug;
use FilamentAddons\Tables\Actions\PreviewAction;
use FilamentAddons\Tables\Columns\TitleWithStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RunwayResource extends Resource
{
    protected static ?string $model = Runway::class;

    protected static ?string $label = 'Landing Page';

    protected static ?string $navigationGroup = 'Airport';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Landing Pages';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TitleWithSlug::make('title', 'slug', '/loans/')->columnSpan('full'),
                Section::make('Details')
                    ->collapsible()
                    ->collapsed(fn ($livewire) => $livewire instanceof EditRecord)
                    ->schema([
                        Select::make('status')
                            ->default('Draft')
                            ->options(Status::class)
                            ->required()
                            ->columnSpan(2),
                        Toggle::make('has_chat')
                            ->columnSpan(2),
                        Timestamps::make(),
                    ]),
                Section::make('Page Content')
                    ->schema([
                        PageBuilder::make('content'),
                    ])->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TitleWithStatus::make('title')
                    ->statuses(Status::class)
                    ->hiddenOn(Status::Published->name)
                    ->colors(Status::colors())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')->label('Last Updated')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Status::class),
                TrashedFilter::make(),
            ])
            ->actions([
                PreviewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
                RestoreAction::make()->iconButton(),
                ForceDeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('title', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRunways::route('/'),
            'create' => CreateRunway::route('/create'),
            'edit' => EditRunway::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
