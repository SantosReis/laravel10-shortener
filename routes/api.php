<?php

use App\Http\Controllers\Api\V2\UrlShortenerController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
})->prefix('v2');

// Protected routes
Route::group(['prefix' => 'v2', 'middleware' => ['auth:sanctum']], function () {

    Route::post('/shortener', [UrlShortenerController::class, 'index']);
    Route::get('/shortener', [UrlShortenerController::class, 'list']);
    Route::delete('/shortener/{id}', [UrlShortenerController::class, 'delete']);
});
