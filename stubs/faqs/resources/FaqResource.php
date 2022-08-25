<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages\CreateFaq;
use App\Filament\Resources\FaqResource\Pages\EditFaq;
use App\Filament\Resources\FaqResource\Pages\ListFaqs;
use App\Models\Faq;
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
use FilamentAddons\Tables\Actions\PreviewAction;
use FilamentAddons\Tables\Columns\TitleWithStatus;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Trov\Components\Meta;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $label = 'FAQ';

    protected static ?string $pluralLabel = 'FAQs';

    protected static ?string $navigationGroup = 'Site';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQs';

    protected static ?string $recordTitleAttribute = 'question';

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TitleWithSlug::make('question', 'slug', '/faqs/')->columnSpan('full'),
            Section::make('Details')
                ->collapsible()
                ->collapsed(fn ($livewire) => $livewire instanceof EditRecord)
                ->schema([
                    Select::make('status')
                        ->default('Draft')
                        ->options(Status::class)
                        ->required(),
                    SpatieTagsInput::make('tags')
                        ->type('faq'),
                    Timestamps::make(),
                ])->columns(['md' => 2]),
            Meta::make()->collapsed(fn ($livewire) => $livewire instanceof EditRecord),
            TiptapEditor::make('answer')->profile('default')->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TitleWithStatus::make('question')
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
            ->defaultSort('question', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
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
