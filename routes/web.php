<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminBorrowingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserBorrowingController;
use App\Http\Controllers\UserItemController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BorrowingPaymentController;
use App\Http\Controllers\EcommerceController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.store');
});

Route::post('/webhook/doku', [PaymentController::class, 'handleDokuCallback'])->name('payments.doku.callback');

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return Auth::user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
});

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard', UserDashboardController::class)
        ->name('dashboard');

    Route::get('/profile', function () {
        return view('user.profile');
    })->name('profile');

    Route::get('/items', [UserItemController::class, 'index'])
        ->name('items.index');

    Route::get('/items/{item}', [UserItemController::class, 'show'])
        ->name('items.show');


    Route::get('/borrowings', [UserBorrowingController::class, 'index'])
        ->name('borrowings.index');

    Route::get('/borrowings/create', [UserBorrowingController::class, 'create'])
        ->name('borrowings.create');

    Route::post('/borrowings', [UserBorrowingController::class, 'store'])
        ->name('borrowings.store');

    Route::get('/borrowings/{borrowing}', [UserBorrowingController::class, 'show'])
        ->name('borrowings.show');


    // PAYMENT BORROWING DOKU
    Route::post('/borrowings/{borrowing}/payment', [BorrowingPaymentController::class, 'initiate'])
        ->name('borrowings.payment');


    Route::get('/chat', [ChatController::class, 'userIndex'])
        ->name('chat.index');

    Route::post('/chat/{chat}/messages', [ChatController::class, 'store'])
        ->name('chat.store');

    Route::get('/notifications', function (Request $request) {
        return view('user.notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(12),
        ]);
    })->name('notifications.index');


    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::middleware(['auth', 'role:user'])->prefix('shop')->name('shop.')->group(function () {
    Route::get('/products', [EcommerceController::class, 'products'])->name('products');
    Route::get('/cart', [EcommerceController::class, 'cart'])->name('cart');
    Route::post('/cart/{item}', [EcommerceController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/items/{cartItem}', [EcommerceController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/items/{cartItem}', [EcommerceController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/checkout', [EcommerceController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{order}', [EcommerceController::class, 'order'])->name('orders.show');
    Route::post('/orders/{order}/pay', [EcommerceController::class, 'pay'])->name('orders.pay');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');

    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('chat.index');
    Route::post('/chat/{chat}/messages', [ChatController::class, 'store'])->name('chat.store');

    Route::get('/notifications', function () {
        return view('admin.notifications.index');
    })->name('notifications.index');

    Route::get('/borrowings', [AdminBorrowingController::class, 'index'])->name('borrowings.index');
    Route::post('/borrowings/{borrowing}/approve', [AdminBorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('/borrowings/{borrowing}/reject', [AdminBorrowingController::class, 'reject'])->name('borrowings.reject');
    Route::post('/borrowings/{borrowing}/returned', [AdminBorrowingController::class, 'returned'])->name('borrowings.returned');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/transactions', [OrderController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{order}', [OrderController::class, 'show'])->name('transactions.show');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'initiatePayment'])->name('orders.payment');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('movements.store');
    Route::post('/payments/{order}', [PaymentController::class, 'initiatePayment'])->name('payments.initiate');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
});
