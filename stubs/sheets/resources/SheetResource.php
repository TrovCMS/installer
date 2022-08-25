<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SheetResource\Pages\CreateSheet;
use App\Filament\Resources\SheetResource\Pages\EditSheet;
use App\Filament\Resources\SheetResource\Pages\ListSheets;
use App\Forms\Components\PageBuilder;
use App\Models\Sheet;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use FilamentAddons\Enums\Status;
use FilamentAddons\Forms\Components\Timestamps;
use FilamentAddons\Forms\Components\TitleWithSlug;
use FilamentAddons\Tables\Actions\PreviewAction;
use FilamentAddons\Tables\Columns\TitleWithStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Trov\Components\Meta;

class SheetResource extends Resource
{
    const ARTICLE_TYPES = ['article' => 'Article', 'resource' => 'Resource'];

    protected static ?string $model = Sheet::class;

    protected static ?string $label = 'Sheet';

    protected static ?string $navigationGroup = 'Site';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TitleWithSlug::make('title', 'slug', fn (?Model $record) => $record ? "/{$record->type}/" : '/')->columnSpan('full'),
                Section::make('Details')
                    ->collapsible()
                    ->collapsed(fn ($livewire) => $livewire instanceof EditRecord)
                    ->columns(['md' => 2])
                    ->schema([
                        Select::make('status')
                            ->default('Draft')
                            ->options(Status::class)
                            ->required(),
                        Select::make('type')
                            ->default('article')
                            ->reactive()
                            ->options(self::ARTICLE_TYPES)->required(),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->required(),
                        DatePicker::make('published_at')
                            ->label('Publish Date'),
                        Timestamps::make(),
                    ]),
                Meta::make()->collapsed(fn ($livewire) => $livewire instanceof EditRecord),
                PageBuilder::make('content')->columnSpan('full'),
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
                IconColumn::make('meta.indexable')
                    ->label('Indexed')
                    ->options([
                        'heroicon-o-check' => true,
                        'heroicon-o-minus' => false,
                    ])
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ]),
                TextColumn::make('type')->enum(self::ARTICLE_TYPES),
                TextColumn::make('published_at')->label('Published At')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Status::class),
                SelectFilter::make('type')->options(self::ARTICLE_TYPES),
                TrashedFilter::make(),
            ])
            ->actions([
                PreviewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSheets::route('/'),
            'create' => CreateSheet::route('/create'),
            'edit' => EditSheet::route('/{record}/edit'),
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
