@php
    $mediaClass = $class ?? '';
    $mimeType = (string) ($media['mime_type'] ?? '');
@endphp

<figure class="problem-media {{ $mediaClass }}">
    <div class="problem-media-frame">
        @if(str_starts_with($mimeType, 'image/'))
            <img src="{{ $media['url'] }}" alt="{{ $media['alt'] ?? '' }}">
        @else
            <a href="{{ $media['url'] }}" target="_blank" rel="noreferrer">
                {{ $media['name'] ?? $media['url'] }}
            </a>
        @endif
    </div>
    @if(!empty($media['caption']))
        <figcaption class="media-caption content-html math-content">
            {!! $media['caption'] !!}
        </figcaption>
    @endif
</figure>
