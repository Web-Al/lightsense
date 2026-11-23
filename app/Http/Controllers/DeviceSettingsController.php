<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceSettingsController extends Controller
{
    // Staff and administrators only (see routes/web.php)
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'mode' => ['nullable', Rule::in(['automatic', 'manual'])],
            'manual_on' => ['nullable', 'boolean'],
            'lux_threshold' => ['nullable', 'integer', 'min:0', 'max:2000'],
        ]);

        $device->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('success', 'Saved. The light picks this up the next time it checks in.');
    }
}
