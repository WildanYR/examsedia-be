<?php

use App\Http\Controllers\AlatTesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageManagerController;
use App\Http\Controllers\KelompokTesController;
use App\Http\Controllers\PsikotesController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\SoalController;
use Illuminate\Support\Facades\Route;

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
// Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/register', [AuthController::class, 'registerStatus']);

// Protected for Peserta
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    // Psikotes
    Route::get('/psikotes/status', [PsikotesController::class, 'getStatusPsikotes']);
    Route::get('/psikotes/kerjakan/{alat_tes_id}', [PsikotesController::class, 'getPsikotes']);
    Route::post('/psikotes/submit/{alat_tes_id}', [PsikotesController::class, 'submitPsikotes']);
});
// Protected for admin
Route::group(['middleware' => ['auth:sanctum', 'admincheck']], function () {
    // Alat Tes
    Route::get('/alat-tes', [AlatTesController::class, 'index']);
    Route::get('/alat-tes/{id}', [AlatTesController::class, 'detail']);
    Route::post('/alat-tes', [AlatTesController::class, 'store']);
    Route::put('/alat-tes/{id}', [AlatTesController::class, 'update']);
    Route::delete('/alat-tes/{id}', [AlatTesController::class, 'remove']);
    // Kelompok Tes
    Route::get('/kelompok-tes', [KelompokTesController::class, 'index']);
    Route::get('/kelompok-tes/{id}', [KelompokTesController::class, 'detail']);
    Route::post('/kelompok-tes', [KelompokTesController::class, 'store']);
    Route::put('/kelompok-tes/{id}', [KelompokTesController::class, 'update']);
    Route::delete('/kelompok-tes/{id}', [KelompokTesController::class, 'remove']);
    // Soal
    Route::get('/soal', [SoalController::class, 'index']);
    Route::post('/soal', [SoalController::class, 'mutate']);
    // Sesi
    Route::get('/sesi', [SesiController::class, 'index']);
    Route::post('/sesi', [SesiController::class, 'store']);
    Route::put('/sesi/{id}', [SesiController::class, 'update']);
    Route::delete('/sesi/{id}', [SesiController::class, 'remove']);
    Route::post('/sesi/disable', [SesiController::class, 'disableAll']);
    // Image Manager
    Route::get('/image-manager', [ImageManagerController::class, 'readDir']);
    Route::post('/image-manager', [ImageManagerController::class, 'uploadImage']);
    Route::delete('/image-manager', [ImageManagerController::class, 'rmImage']);
    Route::post('/image-manager/dir', [ImageManagerController::class, 'makeDir']);
    Route::delete('/image-manager/dir', [ImageManagerController::class, 'rmDir']);

    Route::get('/psikotes/user/{sesi_id}/{alat_tes_id}', [PsikotesController::class, 'getUserPsikotes']);
    Route::get('/psikotes/user/{sesi_id}/{alat_tes_id}/{user_id}', [PsikotesController::class, 'getJawabanUser']);
});