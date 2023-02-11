<?php

use App\Http\Controllers\ConsultationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocietyController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\VaccinationController;

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

Route::prefix('v1')->group(function() {
    Route::post('/auth/login', [SocietyController::class, 'login'])->name('login');
    Route::post('/auth/logout', [SocietyController::class, 'logout'])->name('logout');
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::get('/consultations', [ConsultationController::class, 'show']);
    Route::get('/spots', [SpotController::class, 'index']);
    Route::get('/spots/{spot_id}', [SpotController::class, 'showSpotDetail']);
    Route::post('/vaccinations', [VaccinationController::class, 'store']);
    Route::get('/vaccinations', [VaccinationController::class, 'index']);

    // Route::post('/auth/logout', [SocietyController::class, 'logout'])
    // ->middleware('auth.society')
    // ->name('logout');
});
