<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    // Usage in routes: EnsureRole::class.':staff'  (staff and administrators allowed)
    public function handle(Request $request, Closure $next, string $minimum): Response
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole($minimum), 403, 'Your role does not allow this action.');

        return $next($request);
    }
}
