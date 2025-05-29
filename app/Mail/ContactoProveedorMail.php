<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoProveedorMail extends Mailable
{
    use Queueable, SerializesModels;


    public $subject;
    public $messageBody;

    // Recibe asunto y mensaje en el constructor
    public function __construct($subject, $messageBody)
    {
        $this->subject = $subject;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails')
                    ->with(['messageBody' => $this->messageBody]);
    }


}
