<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main dashboard route
Route::get('/', [WeatherController::class, 'index'])->name('dashboard');
Route::get('/home', fn()=>view('dashboards.index'))->name('home');
Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
Route::get('{any}', [RoutingController::class, 'root'])->name('any');

// Optional future routes (commented out for now)
// Route::get('/location/{name}', [WeatherController::class, 'show'])->name('location.show');
// Route::get('/marine/{name}', [WeatherController::class, 'marine'])->name('marine.show');