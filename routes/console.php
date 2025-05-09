<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CustomerController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Define and schedule the command directly
Artisan::command('customers:expire-to-suspend')->daily()->purpose('Change status of expired customers to suspended');

// Schedule the radius:import command to run at 3:30 PM daily
Artisan::command('radius:import')->dailyAt('15:30')->purpose('Import radius users from radius server');
