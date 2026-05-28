<a class="card-link" href="{{ route('course.show', ['course' => $course['slug'], 'lang' => $currentLang]) }}">
    <article class="surface-card course-card p-4 h-100 {{ $course['featured'] ? 'border-primary' : '' }}">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <p class="muted-label mb-1 text-secondary">{{ __('public.course') }}</p>
                <h3 class="h4 mb-0">{{ $course['title'] }}</h3>
            </div>
            @if($course['is_active'])
                <span class="badge text-bg-success">{{ __('public.active') }}</span>
            @else
                <span class="badge text-bg-secondary">{{ __('public.coming_soon') }}</span>
            @endif
        </div>

        @if($course['description_html'])
            <div class="content-html math-content reader-module-list text-secondary mb-3">
                {!! $course['description_html'] !!}
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between text-secondary small">
            <span>{{ $course['chapter_count'] }} {{ __('public.chapters') }}</span>
            <span>{{ __('public.open') }}</span>
        </div>
    </article>
</a>
