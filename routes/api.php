<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPaswordcontroller;
use Illuminate\Auth\Notifications\ResetPassword;

use function Laravel\Prompts\password;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("v1/auth")->group(function(){

    Route::post("/login",[AuthController::class,"funLogin"]);
    Route::post("/register",[AuthController::class,"funRegister"]);
    
    Route::middleware("auth:sanctum")->group(function(){

        Route::post("/profile",[AuthController::class,"funProfile"]);
        Route::post("/logout",[AuthController::class,"funLogout"]);
    });
  
});


Route::post("reset-password",[ResetPaswordcontroller::class,"resetpassword"]);
Route::post("change-password",[ResetPaswordcontroller::class, "changePassword"]);


Route::get('email/verify/{id}', [AuthController::class, "verify"]) ->name("verification.verify");