<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Device extends Model
{
    protected $fillable = ['name', 'location', 'area', 'latitude', 'longitude', 'api_key_hash', 'last_seen_at'];

    protected $hidden = ['api_key_hash'];

    protected $casts = ['last_seen_at' => 'datetime'];

    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(Reading::class)->latestOfMany('recorded_at');
    }

    // Makes a new secret key. Show it to the admin once, store only its hash.
    public static function newKey(): string
    {
        return 'ls_' . Str::random(40);
    }

    public static function hashKey(string $key): string
    {
        return hash('sha256', $key);
    }

    // A device counts as online if it reported in the last 2 minutes
    public function isOnline(): bool
    {
        return $this->last_seen_at !== null && $this->last_seen_at->gt(now()->subMinutes(2));
    }

    // Has someone set this light's position on the map?
    public function hasPosition(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    // Link that opens the light's position on openstreetmap.org
    public function getMapUrlAttribute(): ?string
    {
        if (! $this->hasPosition()) {
            return null;
        }

        return "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}#map=18/{$this->latitude}/{$this->longitude}";
    }
}
