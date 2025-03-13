<?php

namespace App\Listeners;

use App\Events\UnpaidFeeNotification;
use App\Services\LogService;

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
