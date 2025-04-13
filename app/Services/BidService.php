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
     * Place a bid on a product after validation and process broadcasting.
     */
    public function place(array $data): JsonResponse
    {
        $user = Auth::user();
        $product = Product::with('bids')->findOrFail($data['product_id']);

        if ($this->isAuctionEnded($product)) {
            return $this->error('The auction has ended.');
        }

        if (!$this->isHigherThanCurrent($data['amount'], $product)) {
            return $this->error('Your bid must be higher than the current price.');
        }

        if ($this->isDuplicateBid($user->id, $product, $data['amount'])) {
            return $this->error('You have already placed this bid.');
        }

        if ($this->isBiddingTooFast($user->id, $product)) {
            return $this->error('You are bidding too fast. Wait a few seconds.');
        }

        $previousHighest = $product->highestBid();
        $previousHighestBidderId = $previousHighest?->user_id;
        $previousAmount = $previousHighest?->amount;

        $this->sendOutbidNotification($previousHighestBidderId, $user->id, $product->name, $previousAmount);

        $this->updateProduct($product, $data['amount']);

        $bid = $product->bids()->create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
        ]);

        broadcast(new LiveAuction($bid, $previousHighestBidderId));

        return response()->json([
            'status' => 'success',
            'message' => 'Bid placed successfully.'
        ]);
    }

    /**
     * Send notification to previous highest bidder.
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
     * Check if auction has ended.
     */
    private function isAuctionEnded(Product $product): bool
    {
        return now()->greaterThan($product->end_time);
    }

    /**
     * Check if user has placed same bid for this product already.
     */
    private function isDuplicateBid(int $userId, Product $product, float $amount): bool
    {
        return $product->bids()
            ->where('user_id', $userId)
            ->where('amount', $amount)
            ->exists();
    }

    /**
     * Ensure bid is greater than current price.
     */
    private function isHigherThanCurrent(float $amount, Product $product): bool
    {
        return $amount > $product->current_price;
    }

    /**
     * Prevent spam bidding within short interval.
     */
    private function isBiddingTooFast(int $userId, Product $product): bool
    {
        $lastBid = $product->bids()
            ->where('user_id', $userId)->latest()->first();

        return $lastBid && $lastBid->created_at->diffInSeconds(now()) < 5;
    }

    /**
     * Update product current price and extend end time if needed.
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
     * Return standard error JSON response.
     */
    private function error(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ]);
    }
}
