<?php

namespace App\Forms\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use FilamentCurator\Forms\Components\MediaPicker;

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
