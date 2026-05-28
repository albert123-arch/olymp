@php
    $mediaClass = $class ?? '';
    $mimeType = (string) ($media['mime_type'] ?? '');
    $isImage = str_starts_with($mimeType, 'image/');
    $mediaId = (string) ($media['id'] ?? md5((string) ($media['url'] ?? 'media')));
    $mediaUserKey = auth()->id() ? 'user-' . auth()->id() : 'guest';
    $isRu = ($currentLang ?? app()->getLocale()) === 'ru';
    $resizeLabel = $isRu ? 'Размер' : 'Size';
@endphp

<figure class="problem-media {{ $mediaClass }} {{ $isImage ? 'is-resizable' : '' }}"
        @if($isImage)
            data-problem-media-resizable
            data-problem-media-id="{{ $mediaId }}"
            data-problem-media-user="{{ $mediaUserKey }}"
        @endif>
    <div class="problem-media-frame">
        @if($isImage)
            <img src="{{ $media['url'] }}" alt="{{ $media['alt'] ?? '' }}">
        @else
            <a href="{{ $media['url'] }}" target="_blank" rel="noreferrer">
                {{ $media['name'] ?? $media['url'] }}
            </a>
        @endif
    </div>
    @if($isImage)
        <label class="problem-media-resize">
            <span>{{ $resizeLabel }}</span>
            <input type="range"
                   min="45"
                   max="160"
                   step="5"
                   value="100"
                   data-problem-media-slider
                   aria-label="{{ $resizeLabel }}">
            <span data-problem-media-size-value>100%</span>
        </label>
    @endif
    @if(!empty($media['caption']))
        <figcaption class="media-caption content-html math-content">
            {!! $media['caption'] !!}
        </figcaption>
    @endif
</figure>
