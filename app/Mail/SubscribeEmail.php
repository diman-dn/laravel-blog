<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscribeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    /**
     * Create a new message instance.
     * @param $subs
     */
    public function __construct($subs)
    {
        $this->subscriber = $subs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verify');
    }
}
