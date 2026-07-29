<?php

namespace App\Models;

use App\Enums\BorrowingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'approved_by',
        'status',
        'borrow_date',
        'return_date',
        'notes',
        'approved_at',
        'rejected_at',
        'returned_at',
    ];

    protected $casts = [
        'status' => BorrowingStatus::class,
        'borrow_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(BorrowingItem::class); }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
