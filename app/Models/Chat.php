<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'admin_id', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function admin(): BelongsTo { return $this->belongsTo(User::class, 'admin_id'); }
    public function messages(): HasMany { return $this->hasMany(Message::class); }
}
