<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscoveryArticleResource\Pages\CreateDiscoveryArticle;
use App\Filament\Resources\DiscoveryArticleResource\Pages\EditDiscoveryArticle;
use App\Filament\Resources\DiscoveryArticleResource\Pages\ListDiscoveryArticles;
use App\Forms\Components\DiscoveryPageBuilder;
use App\Models\DiscoveryArticle;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Trov\Components\Meta;

class DiscoveryArticleResource extends Resource
{
    protected static ?string $model = DiscoveryArticle::class;

    protected static ?string $label = 'Article';

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $navigationGroup = 'Discovery Center';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TitleWithSlug::make('title', 'slug', '/discover/')->columnSpan('full'),
                Section::make('Details')
                    ->collapsible()
                    ->collapsed(fn ($livewire) => $livewire instanceof EditRecord)
                    ->columns(['md' => 2])
                    ->schema([
                        Select::make('status')
                            ->default('Draft')
                            ->options(Status::class)
                            ->required(),
                        DatePicker::make('published_at')
                            ->label('Publish Date')
                            ->withoutSeconds(),
                        Select::make('discovery_topic_id')
                            ->relationship('topic', 'title')
                            ->required(),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->required(),
                        Timestamps::make(),
                    ]),
                Meta::make()
                    ->collapsed(fn ($livewire) => $livewire instanceof EditRecord),
                Section::make('Page Content')
                    ->collapsible()
                    ->schema([
                        DiscoveryPageBuilder::make('content'),
                    ]),
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
                TextColumn::make('topic.title')->searchable()->sortable(),
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
                TextColumn::make('published_at')->label('Published At')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Status::class),
                SelectFilter::make('discovery_topic_id')->label('Topic')->relationship('topic', 'title'),
                SelectFilter::make('author_id')->label('Author')->relationship('author', 'name'),
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
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiscoveryArticles::route('/'),
            'create' => CreateDiscoveryArticle::route('/create'),
            'edit' => EditDiscoveryArticle::route('/{record}/edit'),
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
