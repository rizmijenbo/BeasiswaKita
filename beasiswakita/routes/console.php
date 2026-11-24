<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Kirim email pengingat tenggat setiap pagi (jalankan `php artisan schedule:work`).
Schedule::command('reminders:send')->dailyAt('07:00');
