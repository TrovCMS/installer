<?php

namespace App\Filament\Resources\Trov\LinkableResource\Pages;

use App\Filament\Resources\Trov\LinkableResource;
use Filament\Resources\Pages\ListRecords;

class ListLinkables extends ListRecords
{
    protected static string $resource = LinkableResource::class;
}
