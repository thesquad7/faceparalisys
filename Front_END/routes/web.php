<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/input', function () {
    return view('input');
})->name('input');

Route::post('/submit', [PhotoController::class, 'submit'])->name('photo.submit');
