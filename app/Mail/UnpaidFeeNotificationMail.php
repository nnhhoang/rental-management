<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnpaidFeeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $unpaidFees;

    public function __construct($unpaidFees)
    {
        $this->unpaidFees = $unpaidFees;
    }

    public function build()
    {
        return $this->view('emails.unpaid-fees')
            ->subject('Unpaid Rental Fees Notification')
            ->with([
                'unpaidFees' => $this->unpaidFees,
            ]);
    }
}
