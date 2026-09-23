<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{

    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('website.signin')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }

    public function change(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed', // This automatically checks password_confirmation
                'different:current_password',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
        ], [
            'current_password.required' => 'Current password is required',
            'password.required' => 'New password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
            'password.different' => 'New password must be different from current password',
            'password.regex' => 'Password must contain at least one uppercase, one lowercase, one number and one special character'
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect'
            ]);
        }

        // Check if new password is same as current (additional check)
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'New password must be different from current password'
            ]);
        }

        try {
            // Update the password
            $user->password = Hash::make($request->password);
            $user->save();

            // Regenerate session ID to prevent session fixation
            $request->session()->regenerate();

            // Optionally logout other devices (requires laravel/sanctum or similar)
            // Auth::logoutOtherDevices($request->password);

            // Send password change notification (if you have one set up)
            // $user->notify(new PasswordChangedNotification());

            if ($user->phone) {
                $smsMessage = "Your password is changed successfully. Thank you for using our application!";
                $this->smsService->sendSms($user->phone, $smsMessage);
            }
            // Send email
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $smsMessage = "Your password is changed successfully. Thank you for using our application!";
                $this->smsService->sendEmailtoMember($user->email, 'Password Change', $smsMessage);
            }

            return back()->with('success', 'Password changed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to change password. Please try again.');
        }
    }
}
