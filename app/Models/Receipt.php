<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id', 'play_session_id', 'receipt_number',
        'amount', 'issued_at', 'snapshot',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'snapshot' => 'array',
        'amount' => 'decimal:2',
    ];

    public function playSession(): BelongsTo
    {
        return $this->belongsTo(PlaySession::class);
    }
}
