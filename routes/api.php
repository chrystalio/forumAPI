<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\ForumCommentController;
use App\Http\Controllers\v1\ForumController;
use App\Http\Controllers\v1\RegisterController;
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


Route::group(['prefix' => 'v1'], function () {

Route::post('/register', RegisterController::class)->name('register');

Route::group(['middleware' => 'api'], function ($router) {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
    });

    Route::get('forums/tag/{tag}', [ForumController::class, 'filterTag']);

    Route::apiResource('forums', ForumController::class);
    Route::apiResource('forums.comments', ForumCommentController::class);
});
});
