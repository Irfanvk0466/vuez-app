@extends('layouts.master')
@section('title') Live Auctions @endsection

@section('css')
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container" data-auth-user-id="{{ auth()->id() }}">
    <h2>Live Auctions</h2>

    @if(Auth::user()->isBidder())
        <div class="text-center mb-4">
            <h4>Watch Live Auction</h4>
            <iframe 
                width="100%" 
                height="400" 
                src="{{ config('services.youtube.stream_url') }}" 
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
    @endif

    @if($products->isEmpty())
        <p>No active auctions available at the moment.</p>
    @else
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/default.png') }}"
                         class="card-img-top" style="height: 200px; object-fit: cover;" alt="Product Image">

                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p>Starting Price: ${{ $product->starting_price }}</p>
                        <p>Current Price: <span id="current-price-{{ $product->id }}">${{ $product->current_price }}</span></p>
                        <p>Ends In: <span id="timer-{{ $product->id }}"></span></p>

                        <div class="d-flex justify-content-between">
                            @if(Auth::user()->isBidder())
                                <button class="btn btn-danger bid-button" data-id="{{ $product->id }}">Place Bid</button>
                            @endif
                            <a href="{{ route('auctions.bids.index', $product->id) }}" class="btn btn-warning">Show Bidders</a>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    startCountdown({{ $product->id }}, "{{ $product->end_time }}");
                });
            </script>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @endif
</div>

@include('admin.products.bid')
@endsection

@section('script')
<script>
    window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
    window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
    window.placeBidUrl = "{{ route('place-bid') }}";
    window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/auction-live.js') }}"></script>
@endsection
