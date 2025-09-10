<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $recipientName;

    public function __construct($subject, $message, $recipientName)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.custom')
                    ->with([
                        'recipientName' => $this->recipientName,
                        'messageContent' => $this->message,
                    ]);
    }
} 