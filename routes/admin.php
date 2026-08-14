<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SlotController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');

    Route::get('/slots', [SlotController::class, 'index'])->name('admin.slots.index');
    Route::get('/slots/create', [SlotController::class, 'create'])->name('admin.slots.create');
    Route::post('/slots', [SlotController::class, 'store'])->name('admin.slots.store');
    Route::delete('/slots/{slot}', [SlotController::class, 'destroy'])->name('admin.slots.destroy');

    Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
    Route::patch('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('admin.bookings.approve');
    Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('admin.bookings.reject');
});
