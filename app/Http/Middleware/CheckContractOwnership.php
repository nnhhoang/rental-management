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
        $contractId = $request->route('contract') ?? $request->route('id');
        
        if (!$contractId) {
            return response()->json(['message' => 'Contract ID not provided'], 400);
        }
        
        $contract = TenantContract::with('room.apartment')->find($contractId);
        
        if (!$contract) {
            return response()->json(['message' => 'Contract not found'], 404);
        }
        
        if ($contract->room->apartment->user_id !== auth()->id()) {
            return response()->json(['message' => 'You do not have permission to access this contract'], 403);
        }
        
        return $next($request);
    }
}