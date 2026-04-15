<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\FlockController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\DailyRecordController;
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');


    Route::post('flocks/{flock}/submit', [FlockController::class, 'submit'])->name('flocksSubmit');
    Route::post('flocks/{flock}/reject', [FlockController::class, 'reject'])->name('flocksReject');
    Route::post('flocks/{flock}/end', [FlockController::class, 'end'])->name('flocksEnd');
    Route::resource('flocks', FlockController::class)->names([
        'index' => 'generation',
        'create' => 'flocksCreate',
        'store' => 'flocksStore',
        'show' => 'flocksShow',
        'edit' => 'flocksEdit',
        'update' => 'flocksUpdate',
        'destroy' => 'flocksDestroy',
    ])->middleware('can:manage,flock')->parameters(['flocks' => 'flock']);

    Route::resource('daily-records', DailyRecordController::class)->names([
        'index' => 'dailyRecords',
        'create' => 'dailyRecordsCreate',
        'store' => 'dailyRecordsStore',
        'show' => 'dailyRecordsShow',
        'edit' => 'dailyRecordsEdit',
        'update' => 'dailyRecordsUpdate',
        'destroy' => 'dailyRecordsDestroy',
    ])->middleware('can:manage,dailyRecord')->parameters(['daily-records' => 'dailyRecord']);

});

// Inclure les routes modulaires par domaine fonctionnel
require __DIR__.'/settings.php';
