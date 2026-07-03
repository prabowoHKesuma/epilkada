<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* Route::middleware(['auth', 'permission:manage_election'])->group(function () {
    Route::resource('elections', ElectionController::class);
}); */
Route::middleware(['auth', 'permission:manage_user'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
});

require __DIR__.'/auth.php';
