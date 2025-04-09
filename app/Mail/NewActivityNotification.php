<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;



class NewActivityNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $activity;
    public $class;

    public function __construct($activity, $class)
    {
        $this->activity = $activity;
        $this->class = $class;
    }

    public function build()
    {
        return $this->subject('New Activity Posted: ' . $this->activity->title)
                    ->view('emails.new-activity');
    }
}

