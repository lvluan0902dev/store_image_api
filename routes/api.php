<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route Shared
Route::prefix('shared')->namespace('Shared')->group(function () {
    Route::post('upload-single-image', 'ImageController@uploadSingleImage');
    Route::post('get-all-image', 'ImageController@getAllImage');
    Route::delete('delete-single-image/{id}', 'ImageController@deleteSingleImage');
//    Route::get('get-single-image/{image}', 'ImageController@getSingleImage');
});
