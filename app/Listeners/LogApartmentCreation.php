<?php

namespace App\Listeners;

use App\Events\ApartmentCreated;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class LogApartmentCreation
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
    public function handle(ApartmentCreated $event): void
    {
        $userId = Auth::id() ?: $event->apartment->user_id;
        $apartment = $event->apartment;

        $this->logService->createLog(
            $userId,
            'apartment_created',
            "Apartment '{$apartment->name}' was created",
            [
                'apartment_id' => $apartment->id,
                'apartment_name' => $apartment->name,
                'apartment_address' => $apartment->address,
            ]
        );
    }
}
