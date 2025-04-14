<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BidderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashBoardController::class, 'index'])->name('dashboard');

    // Bidding
    Route::post('place-bid', [BidController::class, 'placeBid'])->name('place-bid');
    Route::get('auctions/{product}/bids', [BidController::class, 'index'])->name('auctions.bids.index');
    Route::get('auctions/{product}/bidders/{user}/bids', [BidController::class, 'showUserBids'])->name('auctions.bidders.bids');

    // Chat
    Route::get('/chat/users', [ChatController::class, 'index'])->name('chat-users');
    Route::get('/chat/{bidderId}', [ChatController::class, 'show'])->name('chat-show');
    Route::get('/chat', [ChatController::class, 'showAdminMessage'])->name('chat-showForBidder');
    Route::post('send-message', [ChatController::class, 'sendMessage'])->name('send-message');

    // Notifications
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::get('/checkout/initiate/{product}', [CheckoutController::class, 'index'])->name('checkout.initiate');
    Route::get('/checkout/order/{order}', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/order/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/order/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/order/confirmation', [CheckoutController::class, 'confirmation'])->name('order.confirmation');

});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('/products', ProductController::class);
    Route::get('/bidders', [BidderController::class, 'index'])->name('bidders');
});

// Auth scaffolding
require __DIR__.'/auth.php';
