@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mx-auto" style="max-width: 560px;">
        <h1 class="h4 mb-3">{{ __('public.register') }}</h1>

        <form method="POST" action="{{ route('register.store', ['lang' => $currentLang]) }}" class="d-grid gap-3">
            @csrf
            <div>
                <label class="form-label small text-secondary mb-1" for="name">{{ __('public.name') }}</label>
                <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="form-label small text-secondary mb-1" for="email">{{ __('public.email') }}</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="form-label small text-secondary mb-1" for="password">{{ __('public.password') }}</label>
                <input class="form-control" id="password" name="password" type="password" required>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="form-label small text-secondary mb-1" for="password_confirmation">{{ __('public.password_confirmation') }}</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
            <button class="btn btn-primary" type="submit">{{ __('public.register') }}</button>
        </form>

        <div class="small mt-3">
            <a href="{{ route('login', ['lang' => $currentLang]) }}">{{ __('public.login') }}</a>
        </div>
    </section>
@endsection

