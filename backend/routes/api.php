<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringMapController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->middleware('throttle:60,1');
Route::get('/monitoring-map', MonitoringMapController::class)->middleware('throttle:60,1');
