<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;
use Trov\Concerns\HasCustomEditActions;

class EditPost extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = PostResource::class;
}
