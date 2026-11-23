<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    // Schedules are shared: everyone sees the same calendar.
    public function index()
    {
        $schedules = Schedule::orderBy('start_time')->get();

        return view('dashboard.schedules', compact('schedules'));
    }

    // store, update, toggle and destroy are protected by the "staff" middleware in routes/web.php
    public function store(Request $request)
    {
        Schedule::create($this->validated($request) + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Schedule added.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $schedule->update($this->validated($request));

        return back()->with('success', 'Schedule saved.');
    }

    public function toggle(Schedule $schedule)
    {
        $schedule->update(['enabled' => ! $schedule->enabled]);

        return back()->with('success', $schedule->enabled ? 'Schedule turned on.' : 'Schedule turned off.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Schedule deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ], [
            'name.required' => 'Give the schedule a name.',
            'days.required' => 'Choose at least one day.',
            'days.min' => 'Choose at least one day.',
            'start_time.required' => 'Choose a start time.',
        ]);

        return [
            'name' => $data['name'],
            'days' => implode(',', Schedule::parseDays($data['days'])),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'] ?? null,
            'enabled' => $request->boolean('enabled'),
        ];
    }
}
