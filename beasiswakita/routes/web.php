<?php

use App\Http\Controllers\ScholarshipController;
use Illuminate\Support\Facades\Route;

Route::controller(ScholarshipController::class)->group(function () {
    // 1. Beranda
    Route::get('/', 'home')->name('home');

    // 2. Daftar Beasiswa (?q=&jenjang=&status=)
    Route::get('/beasiswa', 'index')->name('scholarships.index');

    // 3. Detail Beasiswa + form pengingat tenggat
    Route::get('/beasiswa/{scholarship}', 'show')->name('scholarships.show');
    Route::post('/beasiswa/{scholarship}/pengingat', 'storeReminder')
        ->middleware('throttle:10,1')
        ->name('reminders.store');

    // 4. Kalender (?bulan=YYYY-MM)
    Route::get('/kalender', 'calendar')->name('calendar');

    // 5. Panduan Dokumen
    Route::get('/panduan-dokumen', 'guide')->name('guide');
});
