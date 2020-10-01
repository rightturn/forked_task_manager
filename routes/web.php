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


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return redirect('/tasks');
});

Auth::routes();



Route::get('/home', 'HomeController@index')->name('home');

Route::put('/tasks/{task}/toggle','TasksController@toggle')->name('tasks.toggle');

Route::resource('tasks','TasksController');