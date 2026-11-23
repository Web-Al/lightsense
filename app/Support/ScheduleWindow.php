<?php

namespace App\Support;

use App\Models\Schedule;
use Illuminate\Support\Carbon;

// The one rule for schedules, used by the website and by the fake ESP32.
// The real ESP32 firmware must follow the same rule:
//   - If there is no enabled schedule, lights may turn on at any time.
//   - Otherwise lights may turn on only inside an enabled schedule's window.
class ScheduleWindow
{
    public static function allows(array $schedules, Carbon $at): bool
    {
        $enabled = array_filter($schedules, fn ($s) => $s['enabled'] ?? false);

        if (! $enabled) {
            return true;
        }

        foreach ($enabled as $schedule) {
            if (self::inside($schedule, $at)) {
                return true;
            }
        }

        return false;
    }

    private static function inside(array $s, Carbon $at): bool
    {
        $weekday = $at->dayOfWeekIso; // 1 = Monday ... 7 = Sunday

        // "days" is a list of weekday numbers, 1 = Monday ... 7 = Sunday
        if (! in_array($weekday, Schedule::parseDays($s['days'] ?? null), true)) {
            return false;
        }

        $now = $at->hour * 60 + $at->minute;
        [$sh, $sm] = array_map('intval', explode(':', $s['start']));
        $start = $sh * 60 + $sm;

        if (empty($s['end'])) {
            $end = 24 * 60; // "onward" means until the end of the day
        } else {
            [$eh, $em] = array_map('intval', explode(':', $s['end']));
            $end = $eh * 60 + $em;
        }

        return $end > $start
            ? ($now >= $start && $now < $end)
            : ($now >= $start || $now < $end); // window that crosses midnight
    }
}
