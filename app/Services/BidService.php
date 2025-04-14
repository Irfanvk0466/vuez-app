<?php

namespace App\Services;

use App\Events\LiveAuction;
use App\Models\Bid;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\Notification;

class BidService
{
    /**
     * Place a bid on a product after validating auction status, amount, speed, and duplication.
     * Broadcast the bid and send an outbid notification.
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function place(array $data): JsonResponse
    {
        $user = Auth::user();
        $product = Product::with('bids')->findOrFail($data['product_id']);
        if ($error = $this->validateBid($user->id, $product, $data['amount'])) {
            return $this->error($error);
        }
        $previousHighest = $product->highestBid();
        $previousHighestBidderId = $previousHighest?->user_id;
        $previousAmount = $previousHighest?->amount;
        $this->sendOutbidNotification($previousHighestBidderId, $user->id, $product->name, $previousAmount);
        $this->updateProduct($product, $data['amount']);
        $bid = $this->createBid($product, $user->id, $data['amount']);
        broadcast(new LiveAuction($bid, $previousHighestBidderId));
        return response()->json([
            'status' => 'success',
            'message' => 'Bid placed successfully.'
        ]);
    }
    /**
     * Validate bid conditions.
     *
     * @param int $userId
     * @param \App\Models\Product $product
     * @param float $amount
     * @return string|null
     */
    private function validateBid(int $userId, Product $product, float $amount): ?string
    {
        if ($this->isAuctionEnded($product)) {
            return 'The auction has ended.';
        }
        if (!$this->isHigherThanCurrent($amount, $product)) {
            return 'Your bid must be higher than the current price.';
        }
        if ($this->isDuplicateBid($userId, $product, $amount)) {
            return 'You have already placed this bid.';
        }
        if ($this->isBiddingTooFast($userId, $product)) {
            return 'You are bidding too fast. Wait a few seconds.';
        }
        return null;
    }
    /**
     * Create a new bid for the given product.
     *
     * @param \App\Models\Product $product
     * @param int $userId
     * @param float $amount
     * @return \App\Models\Bid
     */
    private function createBid(Product $product, int $userId, float $amount): Bid
    {
        return $product->bids()->create([
            'user_id' => $userId,
            'amount' => $amount,
        ]);
    }
    /**
     * Send outbid notification to the previous highest bidder if applicable.
     *
     * @param int|null $previousBidderId
     * @param int $currentUserId
     * @param string $productName
     * @param float|null $previousAmount
     * @return void
     */
    private function sendOutbidNotification(?int $previousBidderId, int $currentUserId, string $productName, ?float $previousAmount): void
    {
        if ($previousBidderId && $previousBidderId !== $currentUserId && $previousAmount !== null) {
            Notification::create([
                'user_id' => $previousBidderId,
                'title' => 'Outbid Alert',
                'message' => "You’ve been outbid on {$productName}. Previous bid: \${$previousAmount}",
                'type' => 'outbid',
            ]);
        }
    }
    /**
     * Check whether the auction for the given product has ended.
     *
     * @param \App\Models\Product $product
     * @return bool
     */
    private function isAuctionEnded(Product $product): bool
    {
        return now()->greaterThan($product->end_time);
    }
    /**
     * Check whether the user has already placed the same bid amount on the product.
     *
     * @param int $userId
     * @param \App\Models\Product $product
     * @param float $amount
     * @return bool
     */
    private function isDuplicateBid(int $userId, Product $product, float $amount): bool
    {
        return $product->bids()->where('user_id', $userId)->where('amount', $amount)->exists();
    }
    /**
     * Validate that the new bid amount is higher than the current price.
     *
     * @param float $amount
     * @param \App\Models\Product $product
     * @return bool
     */
    private function isHigherThanCurrent(float $amount, Product $product): bool
    {
        return $amount > $product->current_price;
    }
    /**
     * Check if the user is placing bids too quickly (less than 5 seconds apart).
     *
     * @param int $userId
     * @param \App\Models\Product $product
     * @return bool
     */
    private function isBiddingTooFast(int $userId, Product $product): bool
    {
        $lastBid = $product->bids()->where('user_id', $userId)->latest()->first();
        return $lastBid && $lastBid->created_at->diffInSeconds(now()) < 5;
    }
    /**
     * Update the product's current price and extend end time if near closing.
     *
     * @param \App\Models\Product $product
     * @param float $amount
     * @return void
     */
    private function updateProduct(Product $product, float $amount): void
    {
        $product->current_price = $amount;
        $now = now();
        $endTime = Carbon::parse($product->end_time);
        $secondsLeft = $now->diffInSeconds($endTime, false);
        if ($secondsLeft <= 120 && $secondsLeft > 0) {
            $product->end_time = $endTime->copy()->addMinutes(2);
        }
        $product->save();
    }
    /**
     * Return a standardized error response for invalid bid attempts.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    private function error(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ]);
    }
}
