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

Route::get('login', LoginController::class);
//todo: setup forgot password functionality

Route::group(['middleware' => 'auth:sanctum'], function () {
    //all Contact Model routes
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/add', [ContactController::class, 'store']);
    Route::get('/show', [ContactController::class, 'show']);
    Route::post('/update', [ContactController::class, 'update']);
    Route::get('/delete/{id}', [ContactController::class, 'destroy']);
    Route::get('/search_contacts', [ContactController::class, 'filter']);
    Route::get('/unallocated_contacts', [ContactController::class, 'unallocatedContacts']);

    //ContactGroups routes
    Route::get('/contact_groups', [\App\Http\Controllers\GroupController::class, 'index']);
    Route::post('/add_contact_group', [\App\Http\Controllers\GroupController::class, 'store']);
    Route::post('/update_contact_group', [\App\Http\Controllers\GroupController::class, 'update']);
    Route::get('/delete_contact_group/{id}', [\App\Http\Controllers\GroupController::class, 'destroy']);

    //Unallocated contacts

});
