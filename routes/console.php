<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('act:check-database')->everyMinute();
// Schedule::command('sms:check-delivery')->everyMinute();
// Schedule::command('sms:check-delivery')->everyFiveMinutes();
Schedule::command('sms:check-delivery')->everyTenMinutes();
Schedule::command('act:check-digitization')->everyFiveMinutes();
// Schedule::command('sms:check-delivery')->everyFifteenMinutes();
// Schedule::command('sms:check-delivery')->hourly();
Schedule::command('sms:readyCenter-reminder')->dailyAt('10:00');
// Schedule::command('sms:check-delivery')->mondays()->at('09:00') ; 

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
