<?php

use App\Http\Controllers\BreedController;
use App\Http\Controllers\DebtCollectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/debt-collection', [DebtCollectionController::class, 'index']);
    Route::get('/debt-collection/overdue', [DebtCollectionController::class, 'overdue']);
    Route::apiResource('breeds', BreedController::class)->only(['index', 'store', 'update', 'destroy']);
});
