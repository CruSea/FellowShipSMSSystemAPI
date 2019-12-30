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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['namespace'=>'User'], function(){

    Route::post('/register', ['uses'=>'UserController@register']);
    Route::post('/login', ['uses'=>'UserController@login']);
    // Route::post('/register', ['uses'=>'AuthController@register']);
    // Route::post('/login', 'API\AuthCotroller@register');

});
