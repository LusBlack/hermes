<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\ExampleController;
//use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//User routes
Route::get('/', [UserController::class, 'ShowCorrecthomepage'])->name('login');
Route::post('/register', [UserController::class,'register'])->middleware('guest');
Route::post('/login', [UserController::class,'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

//Blog post routes
Route::get('/create-post', [PostController::class,'showCreatePost'])->middleware('mustBeLoggedIn');

Route::post('create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class,'viewSinglePost']);
