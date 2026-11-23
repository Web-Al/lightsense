<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Device extends Model
{
    protected $fillable = ['name', 'location', 'area', 'latitude', 'longitude', 'api_key_hash', 'last_seen_at', 'mode', 'manual_on', 'lux_threshold'];

    protected $hidden = ['api_key_hash'];

    protected $casts = ['last_seen_at' => 'datetime', 'manual_on' => 'boolean'];

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

    // Everything the ESP32 needs to decide on its own, even when it is offline
    public function settingsPayload(): array
    {
        return [
            'mode' => $this->mode,
            'manual_on' => (bool) $this->manual_on,
            'lux_threshold' => (int) $this->lux_threshold,
            'schedules' => Schedule::orderBy('start_time')->get()->map(fn ($s) => [
                'days' => $s->day_numbers,
                'start' => substr($s->start_time, 0, 5),
                'end' => $s->end_time ? substr($s->end_time, 0, 5) : null,
                'enabled' => (bool) $s->enabled,
            ])->values()->all(),
        ];
    }

    // Short code that changes whenever any setting changes.
    // The ESP32 compares it and only downloads settings when it differs.
    public function settingsVersion(): string
    {
        return substr(md5(json_encode($this->settingsPayload())), 0, 8);
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
