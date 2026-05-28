<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id', 'name', 'duration_minutes', 'price',
        'is_unlimited', 'is_active', 'color', 'sort_order',
    ];

    protected $casts = [
        'is_unlimited' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    public function getDurationLabelAttribute(): string
    {
        if ($this->is_unlimited) {
            return 'Unlimited';
        }
        $minutes = (int) $this->duration_minutes;
        if ($minutes % 60 === 0) {
            $hours = intdiv($minutes, 60);
            return $hours.' '.($hours === 1 ? 'hour' : 'hours');
        }
        return $minutes.' min';
    }
}
