<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//backend api route
            
Route::post('/user-Registration',[UserController::class,'userRegistration']);
Route::post('/user-Login',[UserController::class,'userLogin']);
