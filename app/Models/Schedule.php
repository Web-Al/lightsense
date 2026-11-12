<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Schedule extends Model
{
    public const DAYS = [
        'daily' => 'Every day',
        'weekdays' => 'Weekdays',
        'weekends' => 'Weekends',
    ];

    protected $fillable = ['user_id', 'name', 'days', 'start_time', 'end_time', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    // Text shown on the page, e.g. "Every day, 6:00 PM to 11:00 PM"
    public function getSummaryAttribute(): string
    {
        $days = self::DAYS[$this->days] ?? 'Every day';
        $start = Carbon::parse($this->start_time)->format('g:i A');

        if ($this->end_time) {
            return "$days, $start to " . Carbon::parse($this->end_time)->format('g:i A');
        }

        return "$days, $start onward";
    }
}
