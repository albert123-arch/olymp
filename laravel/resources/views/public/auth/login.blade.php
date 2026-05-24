@extends('layouts.public')

@section('content')
    <section class="content-panel p-4 p-lg-5 mx-auto" style="max-width: 520px;">
        <h1 class="h4 mb-3">{{ __('public.login') }}</h1>

        <form method="POST" action="{{ route('login.attempt', ['lang' => $currentLang]) }}" class="d-grid gap-3">
            @csrf
            <div>
                <label class="form-label small text-secondary mb-1" for="email">{{ __('public.email') }}</label>
                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
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
            <button class="btn btn-primary" type="submit">{{ __('public.login') }}</button>
        </form>

        <div class="small mt-3">
            <a href="{{ route('register', ['lang' => $currentLang]) }}">{{ __('public.register') }}</a>
        </div>
    </section>
@endsection

