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

// Route::get('/', function () {
//     return view('upload_pdf');
// });
Route::get('/','FileprocessorController@index')->name('index');
Route::get('/ajxdata','FileprocessorController@sendAjxData')->name('ajxdata');
Route::post('/upload', 'FileprocessorController@upload')->name('upload');
Route::get('test', 'FileprocessorController@test')->name('test');
