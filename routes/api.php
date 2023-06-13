<?php

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

Route::group(['middleware' => ['auth.basic']], static function () {
    Route::group(['prefix' => 'excel'], static function () {
        Route::post('/import', 'App\Http\Controllers\ExcelController@importFile');
        Route::get('/get', 'App\Http\Controllers\ExcelController@showImportedRows');
    });
    Route::post('/user/update/{id}', 'App\Http\Controllers\UserController@update');
});
Route::post('/user/create', 'App\Http\Controllers\UserController@store');


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
