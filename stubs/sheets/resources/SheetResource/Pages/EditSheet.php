<?php

namespace App\Filament\Resources\SheetResource\Pages;

use App\Filament\Resources\SheetResource;
use Filament\Resources\Pages\EditRecord;
use Trov\Concerns\HasCustomEditActions;

class EditSheet extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = SheetResource::class;
}
