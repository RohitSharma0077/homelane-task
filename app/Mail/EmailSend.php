<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailSend extends Mailable
{
    use Queueable, SerializesModels;
    public $mail_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
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
        $subj = $this->mail_data['m_sub'];
        $message = $this->subject($subj)
        ->view('Email.EmailSend')
        ->with($this->mail_data);
        
        //  $message->attach($this->mail_data['attact_pdf'], array(
        //     'as' => 'Submitted_Emp_Details.pdf',
        //     'mime' => 'application/pdf',
        // ));

        return $message;

    }
}
