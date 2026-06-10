<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'supplier_name',
        'recipient_name',
        'total_amount',
        'status',
        'notes',
        'created_by',
        'cancelled_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function scopeWherePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWhereCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWhereCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
