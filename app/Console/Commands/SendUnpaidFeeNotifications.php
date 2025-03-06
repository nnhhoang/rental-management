<?php

namespace App\Console\Commands;

use App\Events\UnpaidFeeNotification;
use App\Services\StatisticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnpaidFeeNotificationMail;

class SendUnpaidFeeNotifications extends Command
{
    protected $signature = 'fees:notify-unpaid';
    protected $description = 'Send email notifications for unpaid fees from the previous month';

    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        parent::__construct();
        $this->statisticsService = $statisticsService;
    }

    public function handle()
    {
        $unpaidFees = $this->statisticsService->getUnpaidRoomsForPreviousMonth();
        
        if ($unpaidFees->isEmpty()) {
            $this->info('No unpaid fees found for the previous month.');
            return 0;
        }
        
        // Group unpaid fees by user/landlord
        $unpaidFeesByUser = $unpaidFees->groupBy(function ($fee) {
            return $fee->room->apartment->user_id;
        });
        
        foreach ($unpaidFeesByUser as $userId => $userFees) {
            $user = $userFees->first()->room->apartment->user;
            
            // Send email to the user with the list of unpaid rooms
            Mail::to($user->email)->send(new UnpaidFeeNotificationMail($userFees));
            
            $this->info("Notification sent to {$user->email} for {$userFees->count()} rooms.");
        }
        
        // Dispatch event for logging
        event(new UnpaidFeeNotification($unpaidFees));
        
        $this->info('Unpaid fee notifications sent successfully.');
        return 0;
    }
}
