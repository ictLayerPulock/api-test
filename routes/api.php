<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FGListController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/', [HomeController::class, 'index']);

Route::get('/get-data', [HomeController::class, 'getData']);

// Route::post('/fg-list',[FGListController::class, 'index']);
Route::post('/fg-list',[FGListController::class, 'index'])->name('list.check');

Route::get('/location',[LocationController::class, 'index']);

Route::post('/location-store',[LocationController::class, 'storeData'])->name('store');


Route::post('/location-update',[LocationController::class, 'update'])->name('update');


Route::get('/location-update-data',[LocationController::class, 'newUpdate']);

Route::post('/delete', [LocationController::class, 'delete'])->name('delete');
