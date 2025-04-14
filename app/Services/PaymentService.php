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
     
         $topBid = $product->bids()
             ->where('user_id', $userId)
             ->orderByDesc('amount')
             ->first();
     
         if (!$topBid) {
             return ['error' => 'Top bid not found.'];
         }
     
         // ✅ Check if a pending payment already exists for this user & product
         $existingPayment = Payment::where('user_id', $userId)
             ->where('product_id', $product->id)
             ->where('status', 'pending')
             ->first();
     
         if ($existingPayment) {
             return [
                 'order_id'     => $existingPayment->order_id,
                 'amount'       => $existingPayment->amount * 100,
                 'currency'     => $existingPayment->currency,
                 'product_name' => $product->name,
             ];
         }
     
         // 🆕 Create Razorpay order
         $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
     
         $order = $api->order->create([
             'receipt'         => Str::uuid()->toString(),
             'amount'          => $topBid->amount * 100,
             'currency'        => 'INR',
             'payment_capture' => 1,
         ]);
     
         // 📝 Save in DB
         $newPayment = Payment::create([
             'user_id'    => $userId,
             'product_id' => $product->id,
             'order_id'   => $order['id'],
             'amount'     => $topBid->amount,
             'currency'   => 'INR',
             'status'     => 'pending',
         ]);
     
         Session::put('razorpay_order_id', $order['id']);
     
         return [
             'order_id'     => $newPayment->order_id,
             'amount'       => $newPayment->amount * 100,
             'currency'     => $newPayment->currency,
             'product_name' => $product->name,
         ];
     }
}     