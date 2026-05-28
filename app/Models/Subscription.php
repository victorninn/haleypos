<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    public const PLAN_TRIAL_1M  = 'trial_1m';
    public const PLAN_MONTH_1   = 'month_1';
    public const PLAN_MONTH_6   = 'month_6';
    public const PLAN_YEAR_1    = 'year_1';
    public const PLAN_LIFETIME  = 'lifetime';

    public const PLANS = [
        self::PLAN_TRIAL_1M  => ['label' => '1 Month Free Trial', 'months' => 1,   'is_trial' => true,  'is_lifetime' => false],
        self::PLAN_MONTH_1   => ['label' => '1 Month',            'months' => 1,   'is_trial' => false, 'is_lifetime' => false],
        self::PLAN_MONTH_6   => ['label' => '6 Months',           'months' => 6,   'is_trial' => false, 'is_lifetime' => false],
        self::PLAN_YEAR_1    => ['label' => '1 Year',             'months' => 12,  'is_trial' => false, 'is_lifetime' => false],
        self::PLAN_LIFETIME  => ['label' => 'Lifetime (10 yrs)',  'months' => 120, 'is_trial' => false, 'is_lifetime' => true],
    ];

    protected $fillable = [
        'business_id', 'plan_type', 'starts_at', 'expires_at',
        'status', 'is_trial', 'is_lifetime', 'notes',
    ];

    protected $casts = [
        'starts_at'   => 'datetime',
        'expires_at'  => 'datetime',
        'is_trial'    => 'boolean',
        'is_lifetime' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function planLabel(): string
    {
        return self::PLANS[$this->plan_type]['label'] ?? ucfirst((string) $this->plan_type);
    }

    public function isExpired(): bool
    {
        if ($this->status === 'cancelled') {
            return true;
        }
        if ($this->is_lifetime) {
            return $this->expires_at !== null && now()->greaterThan($this->expires_at);
        }
        return $this->expires_at !== null && now()->greaterThanOrEqualTo($this->expires_at);
    }

    public function isActive(): bool
    {
        return ! $this->isExpired();
    }

    public function daysRemaining(): ?int
    {
        if (! $this->expires_at) {
            return null;
        }
        if ($this->isExpired()) {
            return 0;
        }
        return (int) floor(now()->floatDiffInDays($this->expires_at, false));
    }

    public function statusBadge(): array
    {
        if ($this->isExpired()) {
            return ['label' => 'Expired', 'tone' => 'red'];
        }
        if ($this->is_trial) {
            return ['label' => 'Trial', 'tone' => 'yellow'];
        }
        if ($this->is_lifetime) {
            return ['label' => 'Lifetime', 'tone' => 'green'];
        }
        return ['label' => 'Active', 'tone' => 'green'];
    }
}
