<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'device_id',
        'message',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
