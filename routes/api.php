<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\PollController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Auth'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('create_poll', [PollController::class, 'createPoll']);
    Route::get('get_poll/{poll}', [PollController::class, 'getPoll']);
    Route::post('take_poll', [PollController::class, 'takePoll']);
    Route::get('list_polls', [PollController::class, 'listPolls']);
    Route::get('list_owner_polls', [PollController::class, 'listOwnerPolls']);
});
