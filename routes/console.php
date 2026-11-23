<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('site:sync steam')->hourly()->withoutOverlapping();
Schedule::command('site:sync mal')->hourly()->withoutOverlapping();
Schedule::command('site:sync github')->hourly()->withoutOverlapping();
// More often, so "now playing" stays roughly current.
Schedule::command('site:sync spotify')->everyFiveMinutes()->withoutOverlapping();
