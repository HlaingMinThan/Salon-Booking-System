<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'index'])->name('home');
Route::post('/booking/slots/{slot}/book', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/search', [BookingController::class, 'search'])->name('booking.search');
Route::get('/booking/confirmation/{booking}', [BookingController::class, 'show'])->name('booking.confirmation');
