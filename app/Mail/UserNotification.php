<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $emailContent; // Renamed from $message to avoid conflict

    public function __construct($subject, $emailContent)
    {
        $this->subject = $subject;
        $this->emailContent = $emailContent;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.user-notification')
                    ->with(['emailContent' => $this->emailContent]);
    }
}