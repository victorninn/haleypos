<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlaySession extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id', 'child_id', 'package_id', 'started_by', 'ended_by',
        'start_time', 'expected_end_time', 'end_time',
        'final_price', 'status', 'extended_minutes', 'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'end_time' => 'datetime',
        'final_price' => 'decimal:2',
        'extended_minutes' => 'integer',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    public function isUnlimited(): bool
    {
        return $this->expected_end_time === null;
    }

    public function remainingSeconds(): ?int
    {
        if ($this->isUnlimited() || $this->status !== 'active') {
            return null;
        }
        $diff = CarbonImmutable::now()->diffInSeconds($this->expected_end_time, false);
        return (int) $diff;
    }

    public function remainingMinutes(): ?int
    {
        $sec = $this->remainingSeconds();
        return $sec === null ? null : (int) ceil($sec / 60);
    }

    public function statusColor(): string
    {
        if ($this->status !== 'active') {
            return 'gray';
        }
        if ($this->isUnlimited()) {
            return 'green';
        }
        $remaining = $this->remainingMinutes();
        if ($remaining === null || $remaining <= (int) config('pos.red_at', 5)) {
            return 'red';
        }
        if ($remaining <= (int) config('pos.yellow_at', 20)) {
            return 'yellow';
        }
        return 'green';
    }
}
