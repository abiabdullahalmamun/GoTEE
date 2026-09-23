<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('We could not find a user with that email address.')]);
        }

        // Generate reset token
        $token = Password::createToken($user);

        // Build reset URL
        $url = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {

            // Send SMS if phone exists
            if ($user->phone) {
                $smsMessage = "Your password reset link: {$url}";
                $this->smsService->sendSms($user->phone, $smsMessage);
            }
            // Send email
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $this->smsService->sendEmailtoMember($user->email, 'Password Reset', $url);
            }

            return back()->with('status', __('We have sent your password reset link via email and SMS (if phone number exists)!'));

        } catch (\Exception $e) {
            return back()->with('error', __('Failed to send password reset links. Please try again later.'));
        }
    }
}
