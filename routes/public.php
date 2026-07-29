<?php

use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\PublicBookController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('/books', [PublicBookController::class, 'index'])->name('public.books.index');
Route::get('/books/{book}', [PublicBookController::class, 'show'])->name('public.books.show');
