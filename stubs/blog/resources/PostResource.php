<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Forms\Components\PageBuilder;
use App\Models\Post;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
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
use FilamentAddons\Forms\Fields\DateInput;
use FilamentAddons\Tables\Actions\PreviewAction;
use FilamentAddons\Tables\Columns\TitleWithStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Trov\Components\Meta;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $label = 'Post';

    protected static ?string $navigationGroup = 'Site';

    protected static ?string $navigationLabel = 'Blog Posts';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TitleWithSlug::make('title', 'slug', '/posts/')->columnSpan('full'),
            Section::make('Details')
                ->collapsible()
                ->collapsed(fn ($livewire) => $livewire instanceof EditRecord)
                ->columns(['md' => 2])
                ->schema([
                    Group::make([
                        Select::make('status')
                            ->default('Draft')
                            ->options(Status::class)
                            ->required()
                            ->columnSpan(2),
                        DateInput::make('published_at')
                            ->label('Publish Date')
                            ->withoutTime()
                            ->columnSpan(2),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->required()
                            ->columnSpan(2),
                    ]),
                    Group::make([
                        SpatieTagsInput::make('tags')
                            ->type('post')
                            ->columnSpan(2),
                        Timestamps::make(),
                    ]),

                ]),
            Meta::make()
                ->collapsed(fn ($livewire) => $livewire instanceof EditRecord),
            Section::make('Post Content')
                ->schema([
                    PageBuilder::make('content'),
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
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
