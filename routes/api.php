<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', LoginController::class);
//todo: setup forgot password functionality

Route::group(['middleware' => 'auth:sanctum'], function () {
    //all Contact Model routes
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/add', [ContactController::class, 'store']);
    Route::get('/show', [ContactController::class, 'show']);
    Route::put('/update', [ContactController::class, 'update']);
    Route::delete('/delete/{id}', [ContactController::class, 'destroy']);
    Route::get('/search_contacts', [ContactController::class, 'filter']);
});
