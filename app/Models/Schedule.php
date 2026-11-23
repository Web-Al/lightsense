<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Schedule extends Model
{
    // Weekday numbers follow ISO: 1 = Monday ... 7 = Sunday
    public const DAY_NAMES = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];

    protected $fillable = ['user_id', 'name', 'days', 'start_time', 'end_time', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    // Turns "1,2,3" (or an array, or the old words daily/weekdays/weekends) into [1, 2, 3]
    public static function parseDays(string|array|null $days): array
    {
        $list = is_array($days) ? $days : match ($days) {
            null, '', 'daily' => [1, 2, 3, 4, 5, 6, 7],
            'weekdays' => [1, 2, 3, 4, 5],
            'weekends' => [6, 7],
            default => explode(',', $days),
        };

        return collect($list)->map(fn ($d) => (int) $d)->filter(fn ($d) => $d >= 1 && $d <= 7)->unique()->sort()->values()->all();
    }

    public function getDayNumbersAttribute(): array
    {
        return self::parseDays($this->days);
    }

    public static function dayLabel(array $days): string
    {
        return match (true) {
            $days === [1, 2, 3, 4, 5, 6, 7] => 'Every day',
            $days === [1, 2, 3, 4, 5] => 'Weekdays',
            $days === [6, 7] => 'Weekends',
            default => implode(', ', array_map(fn ($d) => self::DAY_NAMES[$d], $days)),
        };
    }

    // Text shown on the page, e.g. "Mon, Wed, Fri, 6:00 PM to 11:00 PM"
    public function getSummaryAttribute(): string
    {
        $days = self::dayLabel($this->day_numbers);
        $start = Carbon::parse($this->start_time)->format('g:i A');

        if ($this->end_time) {
            return "$days, $start to " . Carbon::parse($this->end_time)->format('g:i A');
        }

        return "$days, $start onward";
    }
}
