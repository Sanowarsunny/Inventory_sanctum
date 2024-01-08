<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('pages.home');
});

//backend api route
            
Route::post('/user-Registration',[UserController::class,'userRegistration']);