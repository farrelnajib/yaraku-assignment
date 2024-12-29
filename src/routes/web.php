<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Route;

Route::get('/exports/{filename}', function ($filename) {
    $path = storage_path('app/exports/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $mimeType = pathinfo($filename, PATHINFO_EXTENSION);
    $baseName = basename($path);
    return response()->file($path, [
        'Content-Type' => "text/$mimeType; charset=UTF-8",
        'Content-Disposition' => 'attachment; filename="' . $baseName . '"',
    ]);
})->where('filename', '.*');

Route::get("/{any}", function () {
    return view("app");
})->where("any", ".*");
