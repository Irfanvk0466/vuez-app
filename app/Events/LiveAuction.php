<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use App\Models\Bid;

class LiveAuction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;
    public $previousHighestBidderId;
    public $productName;
    public $newEndTime;

    public function __construct(Bid $bid, $previousHighestBidderId)
    {
        $bid = $bid->fresh(['product']); 
        $this->bid = [
            'amount' => $bid->amount,
            'product_id' => $bid->product_id,
            'user_id' => $bid->user_id,
        ];
        $this->previousHighestBidderId = $previousHighestBidderId;
        $this->productName = $bid->product->name;
        $this->newEndTime = $bid->product->end_time->toDateTimeString();
    }

    public function broadcastOn()
    {
        return new Channel('bids');
    }

    public function broadcastWith()
    {
        return [
            'bid' => $this->bid,
            'product_name' => $this->productName,
            'new_end_time' => $this->newEndTime,
            'previous_highest_bidder' => $this->previousHighestBidderId,
        ];
    }
}

