<?php

namespace App\Http\Controllers;

use App\Models\DiscoveryTopic;

class DiscoveryTopicController extends Controller
{
    public function show(DiscoveryTopic $topic)
    {
        abort_unless($topic->status == 'published' || auth()->user(), 404);

        return view('discovery-topic', [
            'topic' => $topic,
        ]);
    }
}
