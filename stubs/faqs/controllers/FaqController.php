<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Spatie\Tags\Tag;

class FaqController extends Controller
{
    public function index()
    {
        $tags = Tag::getWithType('faq');

        $faqs = $tags->map(function ($tag) use ($tags) {
            $fqs = Faq::isPublished()->withAnyTags($tag, 'faq')->get();

            if ($fqs->isEmpty()) {
                $tags->forget($tag->getKey());

                return;
            } else {
                return collect([
                    'tag' => $tag,
                    'faqs' => $fqs,
                ]);
            }
        });

        $faqs = $faqs->filter(function ($item) {
            return $item !== null;
        })->values();

        return view('faqs.index', [
            'faqs' => $faqs,
            'meta' => [
                'title' => 'Frequently Asked Questions',
                'description' => 'A list of our frequently asked questions.',
                'robots' => 'index,follow',
                'ogImage' => null,
            ],
        ]);
    }

    public function show(Faq $faq)
    {
        abort_unless($faq->status == 'published' || auth()->user(), 404);

        return view('faqs.show', [
            'faq' => $faq,
        ]);
    }
}
