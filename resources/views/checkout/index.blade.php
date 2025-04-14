@extends('layouts.master')
@section('title', 'Checkout')

@section('css')
<style>
    .model {
        width: 900px;
        height: 700px;
        background: white;
        color: white;
        position: relative;
        display: flex;
        margin: 30px auto;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }

    .room {
        width: 50%;
        background: url({{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/default.png') }}) no-repeat center center;
        background-size: cover;
        position: relative;
    }

    .text-cover {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.7);
        padding: 20px;
    }

    .text-cover h1 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .text-cover .price {
        color: #e67e22;
        margin-bottom: 10px;
    }

    .text-cover .price span {
        font-size: 1.4rem;
        font-weight: 700;
    }

    .payment {
        width: 50%;
        color: #34495e;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .receipt-box, .payment-info {
        padding: 20px;
    }

    .receipt-box {
        border-bottom: 1px solid #ccc;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #f9f9f9;
    }

    .table {
        width: 100%;
    }

    .table td {
        font-size: 0.9rem;
        padding: 5px 0;
    }

    .table td:last-child {
        text-align: right;
    }
</style>
@endsection

@section('content')
<div class="model">
    <div class="room">
        <div class="text-cover">
            <h1>{{ $product->name }}</h1>
            <p class="price"> ₹{{ number_format($topBid->amount, 2) }} <span>INR</span></p>
            <hr>
            <p>Product Description:</p>
            <p>{{ $product->description }}</p>
        </div>
    </div>
    <div class="payment">
        <div class="receipt-box">
            <h3>Receipt Summary</h3>
            <table class="table">
                <tr><td>Winning Bid</td><td>₹{{ number_format($topBid->amount, 2) }}</td></tr>
                <tr><td>Subtotal</td><td>₹{{ number_format($topBid->amount, 2) }}</td></tr>
                <tfoot>
                    <tr><td><strong>Total</strong></td><td><strong>₹{{ number_format($topBid->amount, 2) }}</strong></td></tr>
                </tfoot>
            </table>
        </div>
        <div class="payment-info">
            <h3>Payment Info</h3>
            <input type="text" value="{{ Auth::user()->name }}" readonly>
            <input type="text" value="{{ Auth::user()->email }}" readonly>
            <button type="button" id="rzp-button1" class="btn btn-lg btn-success">Pay via Razorpay</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById('rzp-button1');
    const orderId = "{{ $order['order_id'] }}";
    
    button.addEventListener("click", function (e) {
        e.preventDefault();

        const handlePaymentSuccess = (response) => {
            fetch(`/checkout/order/${orderId}/pay`, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_id: response.razorpay_payment_id
                })
            })
            .then(res => res.json())
            .then(data => {
                alert("✅ Payment successful! ID: " + response.razorpay_payment_id);
                window.location.href = `/checkout/order/{{ $order['order_id'] }}/success?payment_id=${response.razorpay_payment_id}`;
            })
            .catch(err => {
                console.error("❌ Failed to store payment_id:", err);
                alert("Payment success, but could not store. Contact support.");
            });
        };

        // Step 2: Open Razorpay Checkout
        fetch(`/checkout/order/${orderId}/pay`, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (!data.order_id) {
                alert("Failed to initiate payment.");
                return;
            }

            const options = {
                key: "{{ config('services.razorpay.key') }}",
                amount: data.amount,
                currency: data.currency,
                name: "Live Auction",
                description: "Payment for {{ $product->name }}",
                image: "{{ asset('assets/images/logo-dark.png') }}",
                order_id: data.order_id,
                handler: handlePaymentSuccess,
                prefill: {
                    name: "{{ Auth::user()->name }}",
                    email: "{{ Auth::user()->email }}",
                    contact: "{{ Auth::user()->phone ?? '9999999999' }}"
                },
                theme: {
                    color: "#0ab39c"
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        })
        .catch(err => {
            console.error("❌ Error while initiating Razorpay:", err);
            alert("Something went wrong while initiating Razorpay payment.");
        });
    });
});
</script>
@endpush
