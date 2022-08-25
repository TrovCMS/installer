<?php

namespace App\Filament\Resources\RunwayResource\Pages;

use App\Filament\Resources\RunwayResource;
use Filament\Resources\Pages\EditRecord;
use Trov\Concerns\HasCustomEditActions;

class EditRunway extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = RunwayResource::class;
}
