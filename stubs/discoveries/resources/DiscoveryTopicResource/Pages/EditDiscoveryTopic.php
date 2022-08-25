<?php

namespace App\Filament\Resources\DiscoveryTopicResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DiscoveryTopicResource;
use Trov\Concerns\HasCustomEditActions;

class EditDiscoveryTopic extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = DiscoveryTopicResource::class;
}
