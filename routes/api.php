<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:admin')->get('/admin', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:admin')->get('/authenticated', function (Request $request) {
    return true;
});







/**
 * Sessions
 */
Route::post('/authenticate', [AuthController::class, 'login']);
Route::post('/session/destroy', [AuthController::class, 'logout']);
Route::get('/session/check', [AuthController::class, 'checkSession']);







/**
 * Authenticated
 */
Route::middleware('auth:admin')->group(function() {
    // Users
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::post('/user', [UserController::class, 'saveUser']);
    Route::get('/user/{id}', [UserController::class, 'getUserDetails']);


    // Administrators
    Route::get('/administrators', [AdministratorController::class, 'getAllAdministrators']);
    Route::post('/administrator', [AdministratorController::class, 'saveAdministrator']);
    Route::get('/administrator/{id}', [AdministratorController::class, 'getAdministratorDetails']);
    Route::delete('/administrator/{id}', [AdministratorController::class, 'deleteAdministrator']);


    // Logs
    Route::get('/logs/users', [LogsController::class, 'getLogUsers']);
    Route::get('/logs', [LogsController::class, 'getAllLogs']);
});
