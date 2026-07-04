<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\CandidateController;

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

Route::middleware(['auth', 'permission:manage_election'])->group(function () {
    Route::resource('elections', ElectionController::class);
    Route::patch('/elections/{election}/publish', [ElectionController::class, 'publish'])->name('elections.publish');
    Route::patch('/elections/{election}/close', [ElectionController::class, 'close'])->name('elections.close');
    Route::patch('/elections/{election}/finish', [ElectionController::class, 'finish'])->name('elections.finish');
});

Route::middleware(['auth', 'permission:manage_user'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
});

Route::middleware(['auth', 'permission:manage_candidate'])->group(function () {
    Route::get('/elections/{election}/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
    Route::post('/elections/{election}/candidates', [CandidateController::class, 'store'])->name('candidates.store');
    Route::get('/elections/{election}/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
    Route::put('/elections/{election}/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
    Route::delete('/elections/{election}/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');
});

require __DIR__.'/auth.php';
