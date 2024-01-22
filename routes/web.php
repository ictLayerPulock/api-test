<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FGListController;
use App\Http\Controllers\LocationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', [HomeController::class, 'index']);

Route::get('/get-data', [HomeController::class, 'getData']);

// Route::post('/fg-list',[FGListController::class, 'index']);
Route::post('/fg-list',[FGListController::class, 'index'])->name('list.check');

Route::get('/location',[LocationController::class, 'index']);

Route::post('/location-store',[LocationController::class, 'storeData']);

Route::post('/delete', [LocationController::class, 'delete']);

