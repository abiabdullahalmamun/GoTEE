<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // dd('uu') ; 
        // 1. Check if the user is currently rate limited
        $this->checkTooManyFailedAttempts($request);

        // 2. Find user with additional security checks
        $userData = User::where('email', $request->email)
            ->where('is_active', 1)
            ->first();

        // 3. Handle user not found or not approved
        if (!$userData) {
            $this->incrementLoginAttempts($request);
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Invalid credentials');
        }

        if ($userData->is_approve != 1) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Your account is not approved yet');
        }

        // 4. Check if account is locked
        if ($userData->login_attempts >= config('auth.max_login_attempts', 5)) {
            $lockoutTime = config('auth.lockout_time', 15); // minutes
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', "Account locked. Please contact your administrator.");
        }

        // 5. Attempt authentication
        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $this->incrementLoginAttempts($request);
            $userData->increment('login_attempts');

            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Invalid credentials');
        }

        // 6. Successful login - reset attempts and update last login
        $userData->update([
            'login_attempts' => 0,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();



        // 8. Redirect based on user type
        $routeName = $request->user ? 'website.index' : 'dashboard';
        return redirect()->intended(route($routeName, absolute: false));
    }

    protected function checkTooManyFailedAttempts(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), config('auth.max_login_attempts', 5))) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit(
            $this->throttleKey($request),
            config('auth.lockout_time', 15) * 60
        );
    }

    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email'))).'|'.$request->ip();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
