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

//Handling not existing routes
Route::fallback(function(){
    return response()->json(['message' => 'Route doesn\'t exist, please read the docs for our available routes'], 404);
});
