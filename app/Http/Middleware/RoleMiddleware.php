<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            // Redirect to the user's correct dashboard instead of aborting
            return match (auth()->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'sdo' => redirect()->route('officer.dashboard'),
                'lineman' => redirect()->route('lineman.dashboard'),
                default => redirect()->route('farmer.dashboard'),
            };
        }

        return $next($request);
    }
}
