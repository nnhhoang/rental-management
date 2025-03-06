<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TenantContract;


class CheckContractOwnership
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
            return response()->json(['message' => 'Apartment ID not provided'], 400);
        }
        
        $apartment = Apartment::find($apartmentId);
        
        if (!$apartment) {
            return response()->json(['message' => 'Apartment not found'], 404);
        }
        
        if ($apartment->user_id !== auth()->id()) {
            return response()->json(['message' => 'You do not have permission to access this apartment'], 403);
        }
        
        return $next($request);
    }
}
