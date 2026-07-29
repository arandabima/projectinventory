<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Username atau password tidak sesuai.'])
                ->onlyInput('username');
        }

        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->isUser()) {
            Auth::logout();

            return back()
                ->withErrors(['username' => 'Akun ini bukan user biasa.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard'));
    }

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Username atau password admin tidak sesuai.'])
                ->onlyInput('username');
        }

        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            Auth::logout();

            return back()
                ->withErrors(['username' => 'Akun ini bukan admin.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $redirectUrl = $this->googleRedirectUrl();

        Log::info('Redirecting user to Google OAuth', [
            'route' => 'auth.google.redirect',
            'redirect_url' => $redirectUrl,
            'configured_redirect' => config('services.google.redirect'),
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $redirectUrl = $this->googleRedirectUrl();

        Log::info('Handling Google OAuth callback', [
            'route' => 'auth.google.callback',
            'redirect_url' => $redirectUrl,
            'request_ip' => $request->ip(),
        ]);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            Log::error('Google OAuth callback failed', [
                'exception' => $exception,
                'redirect_url' => $redirectUrl,
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Login Google gagal. Silakan coba lagi.']);
        }

        Log::info('Google OAuth user data retrieved', [
            'google_id' => $googleUser->getId(),
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
        ]);

        $email = $googleUser->getEmail();

        if (! $email) {
            Log::warning('Google OAuth user did not provide email', [
                'google_id' => $googleUser->getId(),
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Akun Google tidak menyediakan email.']);
        }

        Log::info('Looking up local user for Google login', [
            'google_id' => $googleUser->getId(),
            'email' => $email,
        ]);

        /** @var User|null $user */
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            Log::info('Creating new local user from Google account', [
                'google_id' => $googleUser->getId(),
                'email' => $email,
            ]);

            $user = User::create([
                'name' => $googleUser->getName() ?: $email,
                'username' => $this->uniqueUsername($email),
                'email' => $email,
                'password' => Str::password(32),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'role' => 'user',
            ]);
        }

        if ($user->isAdmin()) {
            Log::warning('Blocked admin account from logging in with Google', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()
                ->route('login')
                ->withErrors(['google' => 'Akun admin tidak boleh login menggunakan Google.']);
        }

        if (! $user->google_id) {
            Log::info('Linking existing local user to Google account', [
                'user_id' => $user->id,
                'email' => $user->email,
                'google_id' => $googleUser->getId(),
            ]);

            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'role' => 'user',
            ])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        Log::info('Google login succeeded', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->intended(route('user.dashboard'));
    }

    private function googleRedirectUrl(): string
    {
        return (string) config('services.google.redirect', '');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function uniqueUsername(string $email): string
    {
        $base = Str::of(Str::before($email, '@'))
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_')
            ->limit(40, '')
            ->value() ?: 'user';

        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $username = Str::limit($base, 35, '').'_'.$suffix;
            $suffix++;
        }

        return $username;
    }
}
