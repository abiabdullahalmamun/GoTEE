<?php

// app/Listeners/LogAuthenticationEvents.php
namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Counter;
class LogAuthenticationEvents
{
    public function handleLogin($event)
    {
        $event->user->logAuthEvent('login', [
            'new_values' => [
                'login_at' => now(),
                'ip_address' => request()->ip()
            ]
        ]);
    }

    public function handleLogout($event)
    {
        $event->user->logAuthEvent('logout', [
            'old_values' => [
                'last_active_at' => now(),
                'ip_address' => request()->ip()
            ]
        ]);
         $user = auth()->user()->id ; 
          $updateEx = Counter::where('loginId',$user)->update([
                'loginstate'  => 0, 
                 'enCall'  =>  1, 
                'loginId'  =>  null, 
                'tokenno'  =>  null, 
                'autoMan'  =>  null, 
                'visaType'  =>  null, 
                'stickerType'  =>  null, 
                'updated_at'=>now()
            ]);  
    }

    public function handleFailed($event)
    {
        // For failed login, we might not have a user
        $user = User::where('email', $event->credentials['email'])->first();

        if ($user) {
            $user->logAuthEvent('failed_login', [
                'new_values' => [
                    'attempted_at' => now(),
                    'ip_address' => request()->ip()
                ]
            ]);
        } else {
            // Create a system log if user not found
            AuditLog::create([
                'action' => 'failed_login',
                'model_type' => 'User',
                'new_values' => [
                    'attempted_email' => $event->credentials['email'],
                    'attempted_at' => now(),
                    'ip_address' => request()->ip()
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            [LogAuthenticationEvents::class, 'handleLogin']
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            [LogAuthenticationEvents::class, 'handleLogout']
        );

        $events->listen(
            'Illuminate\Auth\Events\Failed',
            [LogAuthenticationEvents::class, 'handleFailed']
        );
    }
}
