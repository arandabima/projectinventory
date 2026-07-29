<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingItem extends Model
{
    use HasFactory;

    protected $fillable = ['borrowing_id', 'item_id', 'quantity', 'unit_price', 'subtotal'];

    protected $casts = ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function borrowing(): BelongsTo { return $this->belongsTo(Borrowing::class); }
    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
}
