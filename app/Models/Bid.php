<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'amount',
    ];

    /**
     * Get the user who placed the bid.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product for which the bid was placed.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
