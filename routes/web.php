<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//pages route login
Route::get('/userLogin',[UserController::class,'LoginPage'])->name('login');
Route::get('/logout',[UserController::class,'UserLogout'])->middleware('auth:sanctum');
Route::get('/userProfile',[UserController::class,'Profile']);
Route::get('/userRegistration',[UserController::class,'RegistrationPage']);
Route::get('/sendOTP',[UserController::class,'SendOtpPage']);
Route::get('/verifyOTP',[UserController::class,'VerifyOTPPage']);
Route::get('/resetPassword',[UserController::class,'ResetPasswordPage']);


//backend get api login
Route::get('/user-Profile',[UserController::class,'userProfile'])->middleware('auth:sanctum');

//backend post api route
Route::post('/user-Login',[UserController::class,'userLogin']);
Route::post('/user-Registration',[UserController::class,'userRegistration']);
Route::post('/user-Update',[UserController::class,'userUpdate'])->middleware('auth:sanctum');
Route::post('/send-OTP',[UserController::class,'sendOTP']);
Route::post('/verify-OTP',[UserController::class,'verifyOTP']);
Route::post('/reset-Password',[UserController::class,'resetPassword'])->middleware('auth:sanctum');



//category route

Route::get('/categoryPage',[CategoryController::class,'categoryPage'])->middleware('auth:sanctum');
Route::post('/create-Category',[CategoryController::class,'createCategories'])->middleware('auth:sanctum');
Route::get("/list-category",[CategoryController::class,'CategoryList'])->middleware('auth:sanctum');