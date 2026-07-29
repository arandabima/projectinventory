<?php

namespace App\Models;

use App\Models\Borrowing;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_id',
        'borrowing_id',
        'amount',
        'status',
        'payment_method',
        'doku_reference_number',
        'gateway_reference',
        'doku_transaction_id',
        'doku_response_json',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'doku_response_json' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function scopeWhereConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeWherePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
