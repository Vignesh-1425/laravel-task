<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;

Route::group(['prefix' => 'auth'], function(){
    Route::post('login', [AuthController::class, 'login']);
    Route::post('guard', [AuthController::class, 'guard']);
});
	
Route::post('register/getData', [RegisterController::class, 'lists']);
Route::post('register/action', [RegisterController::class, 'action']);
Route::post('register/delete', [RegisterController::class, 'delete']);
Route::post('register/fileupload', [RegisterController::class, 'fileupload']);
	