<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use App\Models\Payment;

class PaymentService
{
    /**
     * Create Razorpay order for the payment initiation.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return array
     */
    public function createOrder(Request $request, Product $product): array
    {
        $userId = Auth::id();
        $topBid = $this->getUserTopBid($product, $userId);
        if (!$topBid) {
            return ['error' => 'Top bid not found.'];
        }
        $existingPayment = $this->getExistingPendingPayment($product->id, $userId);
        if ($existingPayment) {
            return $this->formatPaymentResponse($existingPayment, $product->name);
        }
        $order = $this->createRazorpayOrder($topBid->amount);
        $payment = $this->storePaymentRecord($product->id, $userId, $topBid->amount, $order['id']);
        Session::put('razorpay_order_id', $order['id']);
        return $this->formatPaymentResponse($payment, $product->name);
    }
    /**
     * Get the user's top bid for a given product.
     *
     * @param \App\Models\Product $product
     * @param int $userId
     * @return \App\Models\Bid|null
     */
    private function getUserTopBid(Product $product, int $userId)
    {
        return $product->bids()->where('user_id', $userId)->orderByDesc('amount')->first();
    }
    /**
     * Check for an existing pending payment for the user and product.
     *
     * @param int $productId
     * @param int $userId
     * @return \App\Models\Payment|null
     */
    private function getExistingPendingPayment(int $productId, int $userId): ?Payment
    {
        return Payment::where('user_id', $userId)->where('product_id', $productId)->where('status', 'pending')->first();
    }
    /**
     * Create a Razorpay order using API.
     *
     * @param float $amount
     * @return array
     */
    private function createRazorpayOrder(float $amount): array
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        return $api->order->create([
            'receipt'         => Str::uuid()->toString(),
            'amount'          => $amount * 100,
            'currency'        => 'INR',
            'payment_capture' => 1,
        ]);
    }
    /**
     * Store a new payment record in the database.
     *
     * @param int $productId
     * @param int $userId
     * @param float $amount
     * @param string $orderId
     * @return \App\Models\Payment
     */
    private function storePaymentRecord(int $productId, int $userId, float $amount, string $orderId): Payment
    {
        return Payment::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'order_id'   => $orderId,
            'amount'     => $amount,
            'currency'   => 'INR',
            'status'     => 'pending',
        ]);
    }
    /**
     * Format the payment response for Razorpay frontend usage.
     *
     * @param \App\Models\Payment $payment
     * @param string $productName
     * @return array
     */
    private function formatPaymentResponse(Payment $payment, string $productName): array
    {
        return [
            'order_id'     => $payment->order_id,
            'amount'       => $payment->amount * 100,
            'currency'     => $payment->currency,
            'product_name' => $productName,
        ];
    }
}
