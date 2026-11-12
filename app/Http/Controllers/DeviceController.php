<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    // Everyone who is logged in can see the list and where each light is
    public function index()
    {
        $devices = Device::with('latestReading')->orderBy('location')->get();

        return view('dashboard.devices', compact('devices'));
    }

    // The actions below are limited to administrators in routes/web.php
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $key = Device::newKey();
        $device = Device::create($data + ['api_key_hash' => Device::hashKey($key)]);

        return back()->with('new_key', ['device' => $device->name, 'key' => $key]);
    }

    public function update(Request $request, Device $device)
    {
        $device->update($this->validated($request));

        return back()->with('status', "{$device->name} was updated.");
    }

    public function regenerateKey(Device $device)
    {
        $key = Device::newKey();
        $device->update(['api_key_hash' => Device::hashKey($key)]);

        return back()->with('new_key', ['device' => $device->name, 'key' => $key]);
    }

    public function destroy(Device $device)
    {
        $device->delete(); // its readings are deleted too

        return back()->with('status', "{$device->name} was deleted.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'location' => ['required', 'string', 'max:60'],
            'area' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
        ], [
            'name.required' => 'Give the light a name.',
            'location.required' => 'Say which room the light is in.',
            'latitude.required_with' => 'Enter both latitude and longitude, or leave both empty.',
            'longitude.required_with' => 'Enter both latitude and longitude, or leave both empty.',
        ]);
    }
}
