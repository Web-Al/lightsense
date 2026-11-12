<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reading extends Model
{
    protected $fillable = ['device_id', 'lux', 'presence', 'lights_on', 'voltage', 'current', 'power', 'energy_kwh', 'recorded_at'];

    protected $casts = [
        'presence' => 'boolean',
        'lights_on' => 'boolean',
        'recorded_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
