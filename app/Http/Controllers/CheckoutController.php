<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page for a given product if the authenticated user
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function index(Product $product)
    {
        $topBid = $product->bids()->orderByDesc('amount')->first();
        if (!$topBid || $topBid->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('checkout.index', compact('product', 'topBid'));
    }
    /**
     * Handle successful payment by storing payment details and redirecting the user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $userId = Auth::id();
        $topBid = $product->bids()->where('user_id', $userId)->orderByDesc('amount')->first();
        if (!$topBid) {
            return redirect()->route('dashboard')->with('error', 'Bid not found.');
        }
        $this->createPayment($userId, $product->id, $request->payment_id ?? 'unknown', $topBid->amount);
        return redirect()->route('dashboard')->with('success', 'Payment successful.');
    }
    /**
     * Store payment information in the database.
     *
     * @param int $userId.
     * @param int $productId.
     * @param string $paymentId.
     * @param float $amount.
     * @return void
     */
    private function createPayment(int $userId, int $productId, string $paymentId, float $amount): void
    {
        Payment::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'payment_id' => $paymentId,
            'amount'     => $amount,
            'status'     => 'completed',
        ]);
    }
}
