<?php

namespace App\Services;

use App\Events\LiveAuction;
use App\Models\Bid;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
     * Check if auction has ended.
     */
    private function isAuctionEnded(Product $product): bool
    {
        return now()->greaterThan($product->end_time);
    }

    /**
     * Ensure bid is greater than current price.
     */
    private function isHigherThanCurrent(float $amount, Product $product): bool
    {
        return $amount > $product->current_price;
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
     * Prevent spam bidding within short interval.
     */
    private function isBiddingTooFast(int $userId, Product $product): bool
    {
        $lastBid = $product->bids()
            ->where('user_id', $userId)
            ->latest()
            ->first();
        return $lastBid && $lastBid->created_at->diffInSeconds(now()) < 5;
    }

    /**
     * Update product current price and extend end time if needed.
     */
    private function updateProduct(Product $product, float $amount): void
    {
        $product->current_price = $amount;
        if (now()->diffInMinutes($product->end_time) <= 2) {
            $product->end_time = Carbon::parse($product->end_time)->addMinutes(2);
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
