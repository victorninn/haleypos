<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id', 'child_code', 'name', 'age', 'gender',
        'guardian_name', 'contact_number', 'emergency_contact',
        'notes', 'photo_path',
    ];

    public function playSessions(): HasMany
    {
        return $this->hasMany(PlaySession::class);
    }

    public function activeSession()
    {
        return $this->hasOne(PlaySession::class)->where('status', 'active')->latestOfMany();
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return asset('storage/'.$this->photo_path);
        }
        return asset('assets/child-placeholder.svg');
    }
}
