<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VeryfiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/logintw',[LoginController::class,'logintwo']);
Route::post('/confirmcode',[LoginController::class,'confirmCode']);


 Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',[LoginController::class,'index']);
    Route::get('/logout',[LoginController::class,'destroyT']);
 });