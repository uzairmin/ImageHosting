<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\ConfirmationController;

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

Route::group(['middleware' => "signupAuth"], function()
{
    Route::post('/signup',[SignupController::class,'signingUp']);
});
Route::get('/confirmation/{email}/{token}',[ConfirmationController::class,'confirming']);
Route::post('/login',[LoginController::class,'loggingIn']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();});

Route::post('/check_otp',[ForgetPasswordController::class,'checkOtp']);

Route::group(['middleware' => "forgetPassAuth"], function()
{
    Route::post('/forget_password',[ForgetPasswordController::class,'forgetPassword']);
    Route::post('/new_password',[LoginController::class,'updatePassword']);
});
Route::group(['middleware' => "tokenAuth"], function()
{
    Route::post('/new_name',[LoginController::class,'updateName']);
    Route::post('/email_updated',[LoginController::class,'updateEmail']);
    Route::post('/update_age',[LoginController::class,'updateAge']);
    Route::post('/update_profile',[LoginController::class,'updateProfile']);    
    Route::post('/show_user',[ShowController::class,'showUser']);
    Route::post('/user_deactivate',[SignupController::class,'deactivate']);
    Route::post('/logout',[LogoutController::class,'loggingOut']);
});