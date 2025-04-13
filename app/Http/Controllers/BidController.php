<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuctionRequest;
use App\Models\Bid;
use App\Models\Product;
use App\Services\BidService;
use App\Models\Payment;

class BidController extends Controller
{
    protected $bidService;

    public function __construct(BidService $bidService)
    {
        $this->bidService = $bidService;
    }
    /**
     * Display a list of all bidders for a given product.
     *
     * @param int $productId
     * @return \Illuminate\View\View
     */
    public function index($productId)
    {
        $product = Product::with('bids.user')->findOrFail($productId);
        $bidders = $this->formatBidders($product);
        $winnerId = $this->getWinnerId($product);
        $paymentCompleted = $this->hasPaymentCompleted($productId, $winnerId);
        return view('auction.index', compact('bidders', 'product', 'winnerId', 'paymentCompleted'));
    }
    /**
     * Return all bid amounts placed by a specific user for a given product.
     *
     * @param int $productId
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUserBids($productId, $userId)
    {
        $bids = Bid::where('product_id', $productId)->where('user_id', $userId)->select('amount')->get();
        return response()->json([
            'status' => 'success',
            'data' => $bids
        ]);
    }
    /**
     * Handle bid placement for an auction.
     *
     * @param \App\Http\Requests\AuctionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeBid(AuctionRequest $request)
    {
        return $this->bidService->place($request->all());
    }
    /**
     * Format bidders list with top bid and name.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Support\Collection
     */
    private function formatBidders(Product $product)
    {
        return $product->bids
            ->groupBy('user_id')
            ->map(function ($bids, $userId) {
                $user = $bids->first()->user;
                return [
                    'user_id' => $userId,
                    'name' => $user->name,
                    'top_bid' => $bids->max('amount'),
                ];
            })->values();
    }
    /**
     * Get the winner ID if auction has ended.
     *
     * @param \App\Models\Product $product
     * @return int|null
     */
    private function getWinnerId(Product $product)
    {
        if (now()->greaterThan($product->end_time)) {
            return optional($product->bids()->orderByDesc('amount')->first())->user_id;
        }
        return null;
    }
    /**
     * Check if the payment has been completed by the winner.
     *
     * @param int $productId
     * @param int|null $userId
     * @return bool
     */
    private function hasPaymentCompleted(int $productId, ?int $userId): bool
    {
        if (!$userId) return false;
        return Payment::where('product_id', $productId)
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }
}
