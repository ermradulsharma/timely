<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewSlotBookAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data = [])
    {
        $this->mail_data = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(MAIL_FROM_EMAIL , config('mail.from.name'))
            ->subject('Interview Slot Booked - ' . config('mail.from.name'))
            ->view('mail.interview-slot-book-alert')
            ->with(['mail_data' => $this->mail_data]);
    }
}
