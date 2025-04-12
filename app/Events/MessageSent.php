<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $sender_name;

    /**
     * Create a new event instance.
     */
    public function __construct(Chat $chat)
    {
        // Ensure sender relationship is loaded
        $chat->load('sender');

        $this->chat = $chat;
        $this->sender_name = $chat->sender->name ?? 'Unknown';
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-channel-' . $this->chat->receiver_id),
            new PrivateChannel('chat-channel-' . $this->chat->sender_id),
        ];
    }

    /**
     * Set a custom event name.
     */
    public function broadcastAs(): string
    {
        return 'new-message';
    }

    /**
     * Define what data to send with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'chat' => [
                'sender_id' => $this->chat->sender_id,
                'receiver_id' => $this->chat->receiver_id,
                'message' => $this->chat->message,
                'created_at' => $this->chat->created_at->toDateTimeString(),
            ],
            'sender_name' => $this->sender_name,
        ];
    }
}
