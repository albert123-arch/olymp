<article class="surface-card chapter-card p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
        <div class="flex-grow-1">
            <p class="muted-label mb-1 text-secondary">{{ __('public.chapter') }}</p>
            <h3 class="h4 mb-2">{{ $chapter['title'] }}</h3>
            @if($chapter['description_html'])
                <div class="content-html math-content text-secondary mb-0">
                    {!! $chapter['description_html'] !!}
                </div>
            @endif
        </div>
        <div class="d-flex flex-column align-items-md-end gap-2">
            <span class="badge text-bg-light border">{{ $chapter['problem_count'] }} {{ __('public.problems') }}</span>
            <div class="d-flex flex-wrap gap-2 justify-content-md-end chapter-tabs">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('chapter.show', ['course' => $courseSlug, 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}#theory">{{ __('public.theory') }}</a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('chapter.show', ['course' => $courseSlug, 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}#examples">{{ __('public.examples') }}</a>
                <a class="btn btn-sm btn-primary" href="{{ route('chapter.practice', ['course' => $courseSlug, 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}">{{ __('public.practice') }}</a>
                @if(($chapter['ladders_count'] ?? 0) > 0)
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('ladders.index', ['course' => $courseSlug, 'chapter' => $chapter['slug'], 'lang' => $currentLang]) }}">{{ __('public.ladders') }}</a>
                @endif
            </div>
        </div>
    </div>
</article>
