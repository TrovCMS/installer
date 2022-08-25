<?php

namespace App\Filament\Resources\Trov\LinkableResource\RelationManagers;

use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Filament\Resources\Trov\LinkableResource;
use Filament\Resources\RelationManagers\MorphManyRelationManager;

class LinkablesRelationManager extends MorphManyRelationManager
{
    protected static string $relationship = 'linkables';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Internal Linking Set';

    protected static ?string $title = 'Internal Linking Sets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(LinkableResource::getFormSchema())
            ->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(LinkableResource::getTableColumns())
            ->filters([
                //
            ]);
    }
}
