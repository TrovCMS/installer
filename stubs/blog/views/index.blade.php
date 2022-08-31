<x-layouts.base :meta="$meta">

    @section('header')
        <x-headers.default />
    @endsection

    <div class="py-8 lg:py-12">
        <x-layouts.two-column-right>
            <x-prose>
                @if ($posts)
                    <h1>Recent Blog Posts</h1>
                    <ul>
                        @foreach ($posts as $post)
                            <li>
                                <div>{{ $post['tag']->name }}</div>
                                <ul>
                                    @foreach ($post['post'] as $fq)
                                        <li><a href="{{ route('post.show', $fq) }}">{{ $fq->question }}</a></li>
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
