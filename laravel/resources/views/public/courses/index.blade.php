@extends('layouts.public')

@section('content')
    <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
        <div>
            <p class="muted-label mb-1 text-secondary">{{ __('public.courses') }}</p>
            <h1 class="h2 mb-0">{{ __('public.courses') }}</h1>
        </div>
    </div>

    <div class="row g-3">
        @foreach($courses as $course)
            <div class="col-md-6 col-xl-4">
                @include('public.partials.course-card', ['course' => $course, 'currentLang' => $currentLang, 'fullWidth' => false])
            </div>
        @endforeach
    </div>
@endsection
