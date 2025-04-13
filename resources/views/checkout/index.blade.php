@extends('layouts.master')
@section('title', 'Checkout')

@section('css')
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
<style>
    .checkout-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        overflow: hidden;
        transition: 0.3s ease-in-out;
    }
    .checkout-card:hover {
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.12);
    }
    .checkout-card img {
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card checkout-card">
                <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/default.png') }}"
                     class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">

                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ $product->description }}</p>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item small">
                            <strong>Winner:</strong> {{ Auth::user()->name }}
                        </li>
                        <li class="list-group-item small">
                            <strong>Your Winning Bid:</strong> ₹{{ number_format($topBid->amount, 2) }}
                        </li>
                        <li class="list-group-item small">
                            <strong>Auction Ended:</strong> {{ $product->end_time->format('d M Y, h:i A') }}
                        </li>
                    </ul>
                    <button type="button" id="rzp-button1" class="btn btn-success w-100">Pay via Razorpay</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    console.log("Razorpay Key:", "{{ config('services.razorpay.key') }}");

  document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById('rzp-button1');

    const options = {
      key: "{{ config('services.razorpay.key') }}",
      amount: "{{ $topBid->amount * 100 }}",
      currency: "INR",
      name: "Live Auction",
      description: "Payment for {{ $product->name }}",
      image: "{{ asset('assets/images/logo-dark.png') }}",
      handler: function (response) {
        alert("✅ Payment successful! Payment ID: " + response.razorpay_payment_id);
        window.location.href = "{{ route('checkout.success', $product->id) }}?payment_id=" + response.razorpay_payment_id;
    },
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

    button.addEventListener("click", function (e) {
      rzp.open();
      e.preventDefault();
    });
  });
</script>
@endpush
