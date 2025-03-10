<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Apartment;

class CheckApartmentOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apartmentId = $request->route('apartment') ?? $request->route('id');
        
        if (!$apartmentId) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.apartment.not_found')
            ], 400);
        }
        
        $apartment = Apartment::find($apartmentId);
        
        if (!$apartment) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.apartment.not_found')
            ], 404);
        }
        
        if ($apartment->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.apartment.no_permission')
            ], 403);
        }
        
        return $next($request);
    }
}