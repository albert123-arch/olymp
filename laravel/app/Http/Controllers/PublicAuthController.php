<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicAuthController extends Controller
{
    private ?Collection $activeLanguages = null;

    public function showLogin(Request $request): View
    {
        if (Auth::check()) {
            return redirect()->route('dashboard', ['lang' => $this->setLocale($request)]);
        }

        $lang = $this->setLocale($request);

        return view('public.auth.login', [
            ...$this->baseViewData($lang),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $lang = $this->setLocale($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, false)) {
            throw ValidationException::withMessages([
                'email' => __('public.auth_failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', ['lang' => $lang]));
    }

    public function showRegister(Request $request): View
    {
        if (Auth::check()) {
            return redirect()->route('dashboard', ['lang' => $this->setLocale($request)]);
        }

        $lang = $this->setLocale($request);

        return view('public.auth.register', [
            ...$this->baseViewData($lang),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $lang = $this->setLocale($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => mb_strtolower($validated['email'], 'UTF-8'),
            'password_hash' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard', ['lang' => $lang]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $lang = (string) $request->query('lang', $this->currentLanguage($request));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home', ['lang' => $lang]);
    }

    private function setLocale(Request $request): string
    {
        $requested = $request->query('lang');
        $allowed = $this->languageCodes();
        $selected = is_string($requested) && in_array($requested, $allowed, true)
            ? $requested
            : ($allowed[0] ?? 'ru');

        App::setLocale($selected);

        return $selected;
    }

    private function currentLanguage(Request $request): string
    {
        $requested = $request->query('lang');
        return is_string($requested) ? $requested : app()->getLocale();
    }

    private function languageCodes(): array
    {
        return $this->languages()->pluck('code')->filter()->values()->all();
    }

    private function languages(): Collection
    {
        if ($this->activeLanguages instanceof Collection) {
            return $this->activeLanguages;
        }

        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->activeLanguages = $languages->isNotEmpty()
            ? $languages
            : collect([
                (object) ['code' => 'ru', 'title' => 'Русский'],
                (object) ['code' => 'en', 'title' => 'English'],
            ]);

        return $this->activeLanguages;
    }

    private function baseViewData(string $lang): array
    {
        return [
            'currentLang' => $lang,
            'availableLanguages' => $this->languages(),
        ];
    }
}

