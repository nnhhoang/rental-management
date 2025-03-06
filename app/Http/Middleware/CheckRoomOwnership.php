<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApartmentRoom;

class CheckRoomOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roomId = $request->route('room') ?? $request->route('id');
        
        if (!$roomId) {
            return response()->json(['message' => 'Room ID not provided'], 400);
        }
        
        $room = ApartmentRoom::with('apartment')->find($roomId);
        
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }
        
        if ($room->apartment->user_id !== auth()->id()) {
            return response()->json(['message' => 'You do not have permission to access this room'], 403);
        }
        
        return $next($request);
    }
}
