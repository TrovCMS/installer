<x-base-layout :meta="$meta">
    @if ($page->content)
        <x-block-content :content="$page->content" />
    @endif
</x-base-layout>
