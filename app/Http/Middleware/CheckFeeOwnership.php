<?php

namespace App\Http\Middleware;

use App\Models\RoomFeeCollection;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeeOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $feeId = $request->route('fee') ?? $request->route('id');

        if (! $feeId) {
            return response()->json(['message' => 'Fee ID not provided'], 400);
        }

        $fee = RoomFeeCollection::with('room.apartment')->find($feeId);

        if (! $fee) {
            return response()->json(['message' => 'Fee collection not found'], 404);
        }

        if ($fee->room->apartment->user_id !== auth()->id()) {
            return response()->json(['message' => 'You do not have permission to access this fee collection'], 403);
        }

        return $next($request);
    }
}
