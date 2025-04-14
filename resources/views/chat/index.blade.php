@extends('layouts.master')
@section('title') Chat @endsection

@section('css')
<link href="{{ asset('css/chat.css') }}" rel="stylesheet" />
@endsection

@section('content')
<meta name="auth-id" content="{{ auth()->id() }}">
<meta name="receiver-id" content="{{ $adminId ?? $bidderId }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="msger">
    <header class="msger-header">
        <div class="msger-header-title">
            <i class="fas fa-comment-alt"></i>
            @if(auth()->user()->isAdmin())
                Chat with Bidder: {{ $bidder->name }}
            @else
                Chat with Admin
            @endif
        </div>
        <div class="msger-header-options">
            <span><i class="fas fa-cog"></i></span>
        </div>
    </header>

    <main class="msger-chat" id="chat-box">
        <div id="messages" class="d-flex flex-column">
            @foreach ($messages as $message)
                @php $isOwn = $message->sender_id == auth()->id(); @endphp
                <div class="d-flex {{ $isOwn ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="message {{ $isOwn ? 'text-right' : 'text-left' }}">
                        @unless($isOwn)
                            <strong>{{ $message->sender->name }}</strong><br>
                        @endunless
                        {{ $message->message }}
                        <small>{{ $message->created_at->format('h:i A') }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <form method="POST" id="sendMessage" action="{{ route('send-message') }}" class="msger-inputarea">
        @csrf
        <input id="message-input" type="text" class="msger-input" placeholder="Enter your message..." />
        <button type="submit" class="msger-send-btn">
            <i class="ri-send-plane-fill me-1"></i>Send
        </button>
    </form>
</section>
@endsection

@section('script')
<script>
    window.PUSHER_APP_KEY = "{{ env('PUSHER_APP_KEY') }}";
    window.PUSHER_APP_CLUSTER = "{{ env('PUSHER_APP_CLUSTER') }}";
</script>
<script src="{{ asset('js/chat.js') }}"></script>
@endsection
