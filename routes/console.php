<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule existing commands
Schedule::command('customers:expire-to-suspend')
    ->daily()
    ->appendOutputTo(storage_path('logs/customers-expire-suspend.log'));

// Schedule the radius:import command to run at 3:30 PM daily
Schedule::command('radius:import')
    ->dailyAt('15:30')
    ->appendOutputTo(storage_path('logs/radius-import.log'));
