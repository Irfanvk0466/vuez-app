@extends('layouts.master')
@section('title') Chat @endsection

@section('css')
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
    @foreach($bidders as $bidder)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $bidder->name }}</h5>
                    <p class="card-text">Email: {{ $bidder->email }}</p>
                    <a href="{{ route('chat-show', $bidder->id) }}" class="btn btn-primary">
                        Chat with {{ $bidder->name }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('script')
@endsection
