<?php

use App\Http\Controllers\Member\BookmarkController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\MemberProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('me')->name('member.')->group(function () {
    Route::get('/dashboard', MemberDashboardController::class)->name('dashboard');

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/books/{book}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    Route::get('/profile', [MemberProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [MemberProfileController::class, 'password'])->name('profile.password');
});
