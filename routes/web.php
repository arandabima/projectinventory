<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/webhook/doku', [PaymentController::class, 'handleDokuCallback'])->name('payments.doku.callback');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/kategori/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/pencatatan', [ItemController::class, 'index'])->name('items.index');
    Route::post('/pencatatan/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/pencatatan/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/pencatatan/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/pencatatan/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::post('/pencatatan/movements', [StockMovementController::class, 'store'])->name('movements.store');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'initiatePayment'])->name('payments.initiate');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/messages', [NotificationController::class, 'store'])->name('notifications.store');
    Route::patch('/notifikasi/messages/{message}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
