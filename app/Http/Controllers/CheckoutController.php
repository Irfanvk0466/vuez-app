<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initiates Razorpay order creation and redirects to the secure checkout page using order_id.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Product $product)
    {
        $topBid = $product->bids()
            ->where('user_id', Auth::id())
            ->orderByDesc('amount')
            ->first();

        if (!$topBid || $topBid->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order = $this->paymentService->createOrder(request(), $product);

        return redirect()->route('checkout', ['order' => $order['order_id']]);
    }

    /**
     * Displays the checkout view using the secure order_id.
     *
     * @param string $orderId
     * @return \Illuminate\View\View
     */
    public function show($orderId)
    {
        $payment = Payment::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with('product')
            ->firstOrFail();

        $product = $payment->product;

        $topBid = $product->bids()
            ->where('user_id', Auth::id())
            ->orderByDesc('amount')
            ->first();

        return view('checkout.index', [
            'product' => $product,
            'topBid' => $topBid,
            'order' => [
                'order_id' => $payment->order_id,
                'amount' => $payment->amount * 100,
                'currency' => $payment->currency,
                'product_name' => $product->name,
            ]
        ]);
    }

    /**
     * Used only for Razorpay client verification after payment (no database update).
     *
     * @param \Illuminate\Http\Request $request
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function pay(Request $request, $orderId)
    {
        $payment = Payment::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment record not found.'], 404);
        }

        return response()->json([
            'message' => 'Payment verification successful.',
            'order_id' => $orderId,
            'amount' => $payment->amount * 100,
            'currency' => $payment->currency
        ]);
    }

    /**
     * Finalizes the payment and updates the order record after successful Razorpay confirmation.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request, $orderId)
    {
        $paymentId = $request->query('payment_id', 'unknown');

        $payment = Payment::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($payment && empty($payment->payment_id)) {
            $payment->update([
                'payment_id' => $paymentId,
                'status' => 'completed',
            ]);
        }

        return redirect()->route('order.confirmation', ['payment_id' => $paymentId]);
    }

    /**
     * Displays the payment confirmation page after a successful transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function confirmation(Request $request)
    {
        $payment = Payment::where('payment_id', $request->query('payment_id'))
            ->where('user_id', Auth::id())
            ->with('product')
            ->firstOrFail();

        return view('checkout.confirmation', [
            'product_name' => $payment->product->name ?? 'N/A',
            'amount' => $payment->amount,
        ]);
    }
}
