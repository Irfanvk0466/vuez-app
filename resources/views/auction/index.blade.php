@extends('layouts.master')
@section('title', 'Bidders')

@section('css')
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Bidders</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered w-100 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Top Bid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bidders as $bidder)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $bidder['name'] }}
                                @if ($winnerId === $bidder['user_id'])
                                    <span class="badge bg-success ms-2">Winner</span>
                                    @if ($paymentCompleted)
                                        <span class="badge bg-info ms-1">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark ms-1">Unpaid</span>
                                    @endif
                                @endif
                            </td>
                            <td>₹{{ number_format($bidder['top_bid'], 2) }}</td>
                            <td>
                                <div class="d-flex flex-column flex-md-row gap-2">
                                    <button class="btn btn-sm btn-primary view-bid"
                                            data-user="{{ $bidder['user_id'] }}"
                                            data-product="{{ $product->id }}">
                                        View Bids
                                    </button>

                                    @if ($winnerId === $bidder['user_id'] && auth()->id() === $bidder['user_id'])
                                        @if ($paymentCompleted)
                                            <button class="btn btn-sm btn-success" disabled>Paid</button>
                                        @else
                                            <a href="{{ route('checkout.initiate', ['product' => $product->id]) }}" class="btn btn-sm btn-success">
                                                Pay Now
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bidModal" tabindex="-1" aria-labelledby="bidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bid History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Bidded Amount</th>
                        </tr>
                    </thead>
                    <tbody id="bidTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    window.fetchBidUrlTemplate = "/auctions/:productId/bidders/:userId/bids";
</script>
<script src="{{ asset('js/bidders.js') }}"></script>
@endsection
