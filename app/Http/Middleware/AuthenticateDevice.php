<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Lets a request through only if it carries a valid device key:
//   Authorization: Bearer ls_xxxxxxxx
class AuthenticateDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        // Devices are not browsers, so always answer in JSON
        $request->headers->set('Accept', 'application/json');

        $key = $request->bearerToken();
        $device = $key ? Device::where('api_key_hash', Device::hashKey($key))->first() : null;

        if (! $device) {
            return response()->json(['message' => 'Invalid or missing device key.'], 401);
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }
}
