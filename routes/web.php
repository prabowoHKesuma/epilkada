<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VoterImportController;
use App\Http\Controllers\ElectionVoterController;
use App\Http\Controllers\TpsBoothTokenController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RemoteVerificationController;
use App\Http\Controllers\RemoteVerificationReviewController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
    Route::patch('/elections/{election}/voters/{electionVoter}/channel', [ElectionVoterController::class, 'changeChannel'])->name('election-voters.change-channel');
});

Route::middleware(['auth', 'permission:issue_tps_token','throttle:30,1'])->group(function () {
    Route::get('/tps-tokens', [TpsBoothTokenController::class, 'electionList'])->name('tps-tokens.election-list');
    Route::get('/elections/{election}/tps-tokens', [TpsBoothTokenController::class, 'index'])->name('tps-tokens.index');
    Route::post('/elections/{election}/tps-tokens', [TpsBoothTokenController::class, 'store'])->name('tps-tokens.store');
});

Route::middleware(['throttle:10,1',\App\Http\Middleware\NoCacheVotingPages::class])->group(function () {
    Route::get('/vote', [VotingController::class, 'showTokenForm'])->name('voting.token-form');
    Route::post('/vote/verify', [VotingController::class, 'verifyToken'])->name('voting.verify');
    Route::get('/vote/booth', [VotingController::class, 'showBooth'])->name('voting.booth');
    Route::post('/vote/submit', [VotingController::class, 'submitVote'])->name('voting.submit');
    Route::get('/vote/selesai', [VotingController::class, 'thankyou'])->name('voting.thankyou');

    Route::get('/vote/remote', [VotingController::class, 'showTokenFormRemote'])->name('voting.token-form.remote');
    Route::post('/vote/remote/verify', [VotingController::class, 'verifyTokenRemote'])->name('voting.verify.remote');
    Route::get('/vote/remote/booth', [VotingController::class, 'showBoothRemote'])->name('voting.booth.remote');
    Route::post('/vote/remote/submit', [VotingController::class, 'submitVoteRemote'])->name('voting.submit.remote');

    /* Route::get('/elections/{election}/remote/ajukan', [RemoteVerificationController::class, 'form'])->name('remote.form');
    Route::post('/elections/{election}/remote/ajukan', [RemoteVerificationController::class, 'submit'])->name('remote.submit'); */
    Route::get('/remote/undangan/{invitationToken}', [RemoteVerificationController::class, 'formByInvitation'])->name('remote.form.invitation');
    Route::post('/remote/undangan/{invitationToken}', [RemoteVerificationController::class, 'submitByInvitation'])->name('remote.submit.invitation');
    Route::get('/remote/status', [RemoteVerificationController::class, 'checkStatusForm'])->name('remote.status.form');
    Route::post('/remote/status', [RemoteVerificationController::class, 'checkStatus'])->name('remote.status.check');
});

Route::middleware(['auth', 'permission:view_result'])->group(function () {
    Route::get('/elections/{election}/results', [ResultController::class, 'show'])->name('results.show');
});

Route::middleware(['auth', 'permission:view_audit_log'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});

Route::middleware(['auth', 'permission:manage_menu'])->group(function () {
    Route::resource('menus', MenuController::class)->except(['show']);
});

Route::middleware(['auth', 'permission:verify_remote_voter'])->group(function () {
    Route::get('/remote-review', [RemoteVerificationReviewController::class, 'index'])->name('remote-review.index');
    Route::get('/remote-review/{rv}/document/{type}', [RemoteVerificationReviewController::class, 'viewDocument'])->name('remote-review.document');
    Route::patch('/remote-review/{rv}/approve-1', [RemoteVerificationReviewController::class, 'approveStep1'])->name('remote-review.approve1');
    Route::patch('/remote-review/{rv}/approve-2', [RemoteVerificationReviewController::class, 'approveStep2'])->name('remote-review.approve2');
    Route::post('/remote-review/{rv}/reject', [RemoteVerificationReviewController::class, 'reject'])->name('remote-review.reject');
});

require __DIR__.'/auth.php';
