<x-layouts.base :meta="$meta">

    @section('header')
        <x-headers.default />
    @endsection

    <div class="py-8 lg:py-12">
        <x-layouts.two-column-right>
            <x-prose>
                @if ($faqs)
                    <h1>Frequently Asked Questions</h1>
                    <ul>
                        @foreach ($faqs as $faq)
                            <li>
                                <div>{{ $faq['tag']->name }}</div>
                                <ul>
                                    @foreach ($faq['faqs'] as $fq)
                                        <li><a href="{{ route('faqs.show', $fq) }}">{{ $fq->question }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                @endif
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
