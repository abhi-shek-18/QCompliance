<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductattributeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController;


//Auth Routes
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout'])->name('logout');


//User Routes
Route::resource('User', UserController::class);

Route::post('/assign-roles', [RoleController::class, 'assignRoles'])->name('roles.assign');


//Location Routes
Route::get('location/city_view', [LocationController::class,'cityView'])->name('location.city_view');
Route::resource('location', LocationController::class);
Route::post('location/update',[LocationController::class,'update']);

//Product Routes
Route::resource('product', ProductController::class);
Route::resource('productattribute', ProductattributeController::class);


// Protect routes that require authentication
Route::get('dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth')->name('dashboard');

// Route::get('/', function () {
//     return view('welcome');
// });
