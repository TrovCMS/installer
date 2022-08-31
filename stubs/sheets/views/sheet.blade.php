<x-base-layout :meta="$meta">

    <x-grid>
        <div class="pt-8 lg:pt-12">
            <x-blocks.heading :data="['level' => 'h1', 'content' => $page->title]" />

            <div class="flex items-center justify-between py-2 mt-2 border-t border-b">
                @if ($page->tags)
                    <p class="font-bold">Tagged: {{ $page->tags->implode('name', ', ') }}</p>
                @endif
                <p class="font-bold">Published: <time
                        datetime="{{ $page->published_at }}">{{ $page->published_at->diffForHumans() }}</time></p>
            </div>
        </div>

        @if ($page->content)
            <x-block-content :content="$page->content" />
        @endif

        <x-slot name="sidebar">
            <x-widgets.author :author="$page->author" />
        </x-slot>
    </x-grid>

</x-base-layout>
