<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'name', 'unit', 'current_stock',
        'minimum_stock', 'location', 'description',
        'image_url', 'image_public_id',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->current_stock <= 0) return 'Habis';
        if ($this->current_stock <= $this->minimum_stock) return 'Menipis';
        return 'Aman';
    }
}