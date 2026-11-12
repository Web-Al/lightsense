<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    // Schedules are shared: everyone sees the same list.
    public function index()
    {
        $schedules = Schedule::orderBy('start_time')->get();

        return view('dashboard.schedules', compact('schedules'));
    }

    // store, toggle and destroy are protected by the "staff" middleware in routes/web.php
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'days' => ['required', Rule::in(array_keys(Schedule::DAYS))],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ], [
            'name.required' => 'Give the schedule a name.',
            'start_time.required' => 'Choose a start time.',
        ]);

        Schedule::create($data + ['user_id' => $request->user()->id]);

        return back()->with('status', 'Schedule added.');
    }

    public function toggle(Schedule $schedule)
    {
        $schedule->update(['enabled' => ! $schedule->enabled]);

        return back();
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return back()->with('status', 'Schedule deleted.');
    }
}
