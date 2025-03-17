<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check()) {
            return $next($request);
        }
        
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}