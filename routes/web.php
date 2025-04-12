<?php

use App\Http\Controllers\Admin\BidderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashBoardController::class, 'index'])->name('dashboard');

    Route::post('place-bid', [BidController::class, 'placeBid'])->name('place-bid');
    Route::get('auctions/{auction}/bids', [BidController::class, 'index'])->name('auctions.bids.index');
    Route::get('auctions/{product}/bidders/{user}/bids', [BidController::class, 'showUserBids'])->name('auctions.bidders.bids');

    Route::get('/chat/users', [ChatController::class, 'index'])->name('chat-users');
    Route::get('/chat/{bidderId}', [ChatController::class, 'show'])->name('chat-show');
    Route::get('/chat', [ChatController::class, 'showAdminMessage'])->name('chat-showForBidder');
    Route::post('send-message', [ChatController::class, 'sendMessage'])->name('send-message');
});
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('/products', ProductController::class);
    Route::get('/bidders', [BidderController::class, 'index'])->name('bidders');

});
require __DIR__.'/auth.php';
