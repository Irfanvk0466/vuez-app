@extends('layouts.master')
@section('title') Chat @endsection

@section('css')
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/chat.css') }}" rel="stylesheet" />
@endsection

@section('content')
<meta name="auth-id" content="{{ auth()->id() }}">
<meta name="receiver-id" content="{{ $adminId ?? $bidderId }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mt-5">
    <h2>
        @if(auth()->user()->isAdmin())
            Chat With Bidder: {{ $bidder->name }}
        @else
            Chat With Admin
        @endif
    </h2>

    <div id="chat-box" style="max-height: 400px; overflow-y: auto;">
        <div id="messages">
            @foreach ($messages as $message)
                <div>
                    @if ($message->sender_id == auth()->id())
                        <div class="message text-right">
                            <strong>You:</strong> {{ $message->message }}<br>
                            <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                        </div>
                    @else
                        <div class="message text-left">
                            <strong>{{ $message->sender->name }}:</strong> {{ $message->message }}<br>
                            <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <form method="POST" id="sendMessage" action="{{ route('send-message') }}" class="mt-3">
        @csrf
        <textarea id="message-input" class="form-control" placeholder="Type your message..." rows="3"></textarea>
        <div class="d-flex justify-content-end mt-2 me-2">
            <button type="submit" class="btn btn-success btn-rounded d-flex align-items-center">
                <i class="ri-send-plane-fill me-2"></i> Send
            </button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
    window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
</script>
<script src="{{ asset('js/chat.js') }}"></script>
@endsection
