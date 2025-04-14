@extends('layouts.master')
@section('title', 'Order Confirmation')

@section('css')
<link rel="stylesheet" href="{{ asset('css/confirmation.css') }}">
@endsection

@section('content')
<div class="bg">

    <div class="card">
        <span class="card__success"><i class="fas fa-check"></i></span>

        <h1 class="card__msg">Payment Complete</h1>
        <p class="card__msg">Thank you! Your auction payment was successful.</p>

        <h2 class="card__submsg">
            Your order has been confirmed. Below is a summary of your transaction:
        </h2>

        <div class="card__body">
            <div class="mt-4 text-start">
                <p class="card__recipient">{{ Auth::user()->name }}</p>
                <p class="card__email">{{ Auth::user()->email }}</p>
                <p class="card__recipient"><strong>Product:</strong> {{ $product_name }}</p>
                <p class="card__recipient"><strong>Amount Paid:</strong> ₹{{ number_format($amount, 2) }}</p>
            </div>
        </div>

        <div class="card__tags">
            <span class="card__tag">completed</span>
        </div>
    </div>

    <!-- Back to Dashboard Button -->
    <div class="text-center mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2">
            ← Back to Dashboard
        </a>
    </div>

</div>
@endsection
