<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

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
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');
Route::get('/change-username', [UserController::class, 'ShowchangeUsername'])->middleware('mustBeLoggedIn');
Route::put('/change-username', [UserController::class, 'changeUsername'])->middleware('mustBeLoggedIn');

//Blog post routes
Route::get('/create-post', [PostController::class,'showCreatePost'])->middleware('mustBeLoggedIn');
Route::post('create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class,'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'delete']);
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm']);
Route::put('/post/{post}', [PostController::class, 'actuallyUpdate']);


//profile related routes
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']);
Route::get('/profile/{user:username}/following', [UserController::class, 'profileFollowing']);

//follow related routes
Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');


//testing gate
Route::get('/admins-only', function(){
    if(Gate::allows('visitAdminPages')) {
        return  'Only admins should be able to see this page';
    }
    return 'you cannot view this page';
});

