<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'payment_id',
        'amount',
        'currency',
        'status',
        'order_id'
    ];

    /**
     * Get the product associated with the payment.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
