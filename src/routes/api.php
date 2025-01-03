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

Route::prefix('v1')->group(function () {
    Route::get('/books', 'api\BookController@index');
    Route::post('/books','api\BookController@store');
    Route::put('/books/{id}','api\BookController@update');
    Route::delete('/books/{id}','api\BookController@delete');
    Route::post('/books/export', 'api\BookController@export');
    Route::get('/books/export/{id}', 'api\BookController@getExportJobById');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
