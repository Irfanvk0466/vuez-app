<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuctionRequest;
use App\Models\Bid;
use App\Models\Product;
use App\Services\BidService;

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

        $bidders = $product->bids
            ->groupBy('user_id')
            ->map(function ($bids, $userId) {
                $user = $bids->first()->user;
                return [
                    'user_id' => $userId,
                    'name' => $user->name,
                    'top_bid' => $bids->max('amount'),
                ];
            })->values();

        return view('auction.index', compact('bidders', 'product'));
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
        $bids = Bid::where('product_id', $productId)
                   ->where('user_id', $userId)
                   ->select('amount')
                   ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bids
        ]);
    }
    /**
     * Handle bid placement for an auction.
     *
     * @param \App\Http\Requests\AuctionRequest $request
     */
    public function placeBid(AuctionRequest $request)
    {
        return $this->bidService->place($request->all());
    }
}
