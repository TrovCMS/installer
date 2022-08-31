<x-layouts.base :meta="$faq->meta">

    @section('header')
        <x-headers.default />
    @endsection

    <div class="py-8 lg:py-12">
        <x-layouts.two-column-right>
            <x-prose>
                <h1>{{ $faq->question }}</h1>
                {!! $faq->answer !!}
            </x-prose>

            <x-slot name="sidebar">
                <x-widget heading="Check out our awesome blog">
                    <p>When we get around to writing some posts.</p>
                </x-widget>
            </x-slot>

        </x-layouts.two-column-right>
    </div>

    @section('footer')
        <x-footers.default />
    @endsection

</x-layouts.base>