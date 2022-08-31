<?php

namespace App\Filament\Resources\DiscoveryTopicResource\Pages;

use App\Filament\Resources\DiscoveryTopicResource;
use Filament\Resources\Pages\EditRecord;
use Trov\Concerns\HasCustomEditActions;

class EditDiscoveryTopic extends EditRecord
{
    use HasCustomEditActions;

    protected static string $resource = DiscoveryTopicResource::class;
}
