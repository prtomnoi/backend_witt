<?php

use App\Http\Controllers as AppController;
use App\Http\Controllers\SuperAdmin as AppSuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\HandleAuth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ContactMessageController;

Route::get('/login', AppController\AuthController::class . '@loginForm')->name('login');
Route::post('/login', AppController\AuthController::class . '@login');
Route::get('/logout', AppController\AuthController::class . '@logout')->name('logout');
Route::middleware(HandleAuth::class)->group(function(){
    // admin & staff
    Route::get('/', [AppController\AuthController::class, 'loginForm']);
    Route::resource('profiles', AppController\AccountsController::class);
    Route::resource('attachment', AppController\AttachmentController::class);
    // superadmin function
    Route::resource('permission', AppSuperAdminController\PermissionController::class);
    Route::resource('user', UserController::class);
    Route::resource('banners', BannerController::class);
    Route::post('/banners/{id}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
    Route::get('/contacts', [ContactMessageController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [ContactMessageController::class, 'show'])->name('contacts.show');
});


