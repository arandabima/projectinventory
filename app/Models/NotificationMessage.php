<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    use HasFactory;

    protected $fillable = ['channel', 'recipient', 'subject', 'message', 'status', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
