<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->tokenCan('admin')) {
            return $next($request);
        }
        
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }
        
        return response()->json([
            'message' => 'Access denied. Administrator privileges required.',
            'status' => 'error',
        ], 403);
    }
}