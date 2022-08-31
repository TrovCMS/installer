<div x-data="{
    decodeHTML(html) {
            var txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        },
        copyEmbedCode: function() {
            let parsedText = this.decodeHTML({{ $embedCode }});
            navigator.clipboard.writeText(parsedText).then(
                function() {
                    window.alert('Success! The embed code was copied to your clipboard');
                },
                function() {
                    window.alert('Opps! Your browser does not support the Clipboard API');
                }
            );
        }
}" x-id="['infographic']" class="mt-4 render-block render-block__infographic">
    <a href="{{ $media->url }}" target="_blank" rel="noopener">
        <img src="{{ $media->url }}" alt="{{ $media->alt }}" width="{{ $media->width }}"
            height="{{ $media->height }}" srcset="
                {{ $media->url }} 1200w,
                {{ $media->large_url }} 1024w,
                {{ $media->medium_url }} 640w
            " sizes="(max-width: 1200px) 100vw, 1200px" loading="lazy"
            @if ($transcript) :aria-describedby="$id('infographic') + '-transcript'" @endif
            class="border" />
    </a>

    <div class="mt-4 embed-infographic">
        <p>{{ __('Would you like to embed this infographic on your site?') }}</p>
        <button type="button" class="mt-2 btn btn-primary" x-on:click="copyEmbedCode">Copy Embed Code</button>
    </div>

    @if ($transcript)
        <div :id="$id('infographic') + '-transcript'" class="sr-only"
            aria-label="extended text alternative for infographic">
            {!! $transcript !!}
        </div>
    @endif

</div>
