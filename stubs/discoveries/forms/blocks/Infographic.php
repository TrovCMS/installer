<?php

namespace App\Forms\Blocks;

use Illuminate\Support\HtmlString;
use FilamentCurator\Forms\Components\MediaPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Builder\Block;

class Infographic
{
    public static function make(): Block
    {
        return Block::make('infographic')
            ->schema([
                MediaPicker::make('image')
                    ->label('Image'),
                Textarea::make('transcript')
                    ->label('Transcript'),
            ]);
    }
}
