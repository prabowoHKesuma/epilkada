<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VoterImportController;
use App\Http\Controllers\ElectionVoterController;

Route::get('/', function () {
    return view('auth.login');
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

Route::middleware(['auth', 'permission:manage_voter'])->group(function () {
    Route::resource('voters', VoterController::class)->except(['show']);
    Route::get('/voters/import', [VoterImportController::class, 'form'])->name('voters.import.form');
    Route::post('/voters/import', [VoterImportController::class, 'process'])->name('voters.import.process');
    Route::get('/elections/{election}/voters', [ElectionVoterController::class, 'index'])->name('election-voters.index');
    Route::post('/elections/{election}/voters', [ElectionVoterController::class, 'store'])->name('election-voters.store');
    Route::delete('/elections/{election}/voters/{electionVoter}', [ElectionVoterController::class, 'destroy'])->name('election-voters.destroy');
});

require __DIR__.'/auth.php';
