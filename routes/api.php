<?php

use App\Http\Controllers\KlubSepakbolaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupporterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk Login

Route::controller(AuthController::class)->group(function() {
    Route::post('login', 'login');
});

// Route untuk semua API supporter
Route::controller(SupporterController::class)->group(function() {
    Route::post('/supporter', 'create')->name('file');
    Route::get('/supporter', 'read');
    Route::put('/supporter/{id_supporter}', 'update');
    Route::delete('/supporter/{id_supporter}', 'delete');
});

// Route untuk semua API klub sepakbola
Route::controller(KlubSepakbolaController::class)->group(function() {
    Route::post('/klub-sepakbola', 'create');
    Route::get('/klub-sepakbola', 'read');
    Route::put('/klub-sepakbola/{id_klub}', 'update');
    Route::delete('/klub-sepakbola/{id_klub}', 'delete');
});
