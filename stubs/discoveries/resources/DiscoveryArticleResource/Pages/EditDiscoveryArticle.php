<?php

namespace App\Filament\Resources\DiscoveryArticleResource\Pages;

use App\Filament\Resources\DiscoveryArticleResource;
use Filament\Resources\Pages\EditRecord;
use Trov\Concerns\HasCustomEditActions;

class EditDiscoveryArticle extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = DiscoveryArticleResource::class;
}
