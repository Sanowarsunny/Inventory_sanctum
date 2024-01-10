<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//view page
//Route::view('/user-Login','pages.auth.login-page')->name('login');
//pages route
Route::get('/userLogin',[UserController::class,'LoginPage'])->name('login');
Route::get('/logout',[UserController::class,'UserLogout'])->middleware('auth:sanctum');
Route::get('/userProfile',[UserController::class,'Profile']);

Route::get('/userRegistration',[UserController::class,'RegistrationPage']);
Route::get('/sendOTP',[UserController::class,'SendOtpPage']);
Route::get('/verifyOTP',[UserController::class,'VerifyOTPPage']);
Route::get('/resetPassword',[UserController::class,'ResetPasswordPage'])->middleware('auth:sanctum');


//backend get api
Route::get('/user-Profile',[UserController::class,'userProfile'])->middleware('auth:sanctum');
Route::get('/user-Update',[UserController::class,'userUpdate'])->middleware('auth:sanctum');


//backend post api route
Route::post('/user-Login',[UserController::class,'userLogin']);
Route::post('/user-Registration',[UserController::class,'userRegistration']);
Route::post('/send-OTP',[UserController::class,'sendOTP']);
Route::post('/verify-OTP',[UserController::class,'verifyOTP']);
Route::post('/reset-Password',[UserController::class,'resetPassword'])->middleware('auth:sanctum');

