<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
/* use App\Http\Controllers\ElectionController; */

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
Route::middleware(['auth', 'permission:manage_election'])->group(function () {
    Route::get('/test-permission', [TestController::class, 'index']);
});

require __DIR__.'/auth.php';
