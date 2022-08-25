<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Resources\Pages\EditRecord;
use Trov\Concerns\HasCustomEditActions;

class EditFaq extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = FaqResource::class;
}
