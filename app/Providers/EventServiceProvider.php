<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listeners\LogAuthenticationEvents;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::subscribe(LogAuthenticationEvents::class);
    }
}
