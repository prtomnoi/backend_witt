<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailjetServiceController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\Api as AppApi;
use App\Http\Middleware\Api;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\VisitorApiController;
Route::get('/', function(Request $request){
    return response()->json(['message' => 'Welcome Api']);
});



Route::post('/contact', [ContactMessageController::class, 'store']);
Route::get('/contact', [ContactMessageController::class, 'index']);
Route::get('/banners', [BannerController::class, 'index']);
Route::post('/visitor', [VisitorApiController::class, 'store']);
