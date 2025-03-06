<?php

namespace App\Listeners;

use App\Events\RoomCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class LogRoomCreation
{
    /**
     * Create the event listener.
     */
    protected $logService;
    
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Handle the event.
     */
    public function handle(RoomCreated $event): void
    {
        $userId = Auth::id() ?: $event->room->apartment->user_id;
        $room = $event->room;

        $this->logService->createLog(
            $userId,
            'room_created',
            "Room '{$room->room_number}' was created for apartment '{$room->apartment->name}'",
            [
                'apartment_id' => $room->apartment_id,
                'apartment_name' => $room->apartment->name,
                'room_id' => $room->id,
                'room_number' => $room->room_number,
            ]
        );
    }
}
