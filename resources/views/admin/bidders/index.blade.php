@extends('layouts.master')
@section('title') Bidders @endsection

@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="p-3">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Bidders</h4>
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search by name or email">
            </div>

            <div class="card-body">
                <table class="table table-bordered align-middle" id="bidderTable">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bidders as $index => $bidder)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $bidder->name }}</td>
                            <td>{{ $bidder->email }}</td>
                            <td>{{ $bidder->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No bidders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/search.js') }}"></script>
@endsection
