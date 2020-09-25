<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

//v1 
// // Route::get('/callback/login', 'LineLoginController@LoginCallBack');
// Route::get('/callback', 'LineLoginController@LoginCallBack');




Route::get('/linelogin', 'LineLoginController@lineLogin')->name('linelogin');
Route::get('/callback', 'LineLoginController@callback')->name('callback');
// Route::get('/', function () {
//     return view('welcome');
// });


// Route::post('/line/webhook', 'LineTestController@webhook')->name('line.webhook');

Route::post('/webhook','LineTestController@webhook');

2