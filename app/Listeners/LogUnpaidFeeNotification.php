<?php

namespace App\Listeners;

use App\Events\UnpaidFeeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class LogUnpaidFeeNotification
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
    public function handle(UnpaidFeeNotification $event): void
    {
        $unpaidFees = $event->unpaidFees;
        $roomCount = count($unpaidFees);

        $this->logService->createLog(
            1, // Admin ID
            'unpaid_fee_notification',
            "Notification sent for {$roomCount} rooms with unpaid fees",
            [
                'unpaid_room_count' => $roomCount,
                'unpaid_room_ids' => $unpaidFees->pluck('apartment_room_id')->toArray(),
            ]
        );
    }
}
