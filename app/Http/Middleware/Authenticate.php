<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // If it's an API request, return null to trigger a 401 Unauthorized response
        if ($request->expectsJson()) {
            return null;
        }

        // For web routes, redirect to login page
        return route('login');
    }
}
