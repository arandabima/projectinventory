<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/pencatatan', [ItemController::class, 'index'])->name('items.index');
Route::post('/pencatatan/items', [ItemController::class, 'store'])->name('items.store');
Route::get('/pencatatan/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
Route::put('/pencatatan/items/{item}', [ItemController::class, 'update'])->name('items.update');
Route::post('/pencatatan/movements', [StockMovementController::class, 'store'])->name('movements.store');

Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
Route::post('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifikasi/messages', [NotificationController::class, 'store'])->name('notifications.store');
Route::patch('/notifikasi/messages/{message}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
